<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\FilterHandler;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AndXHandler;
use Yiisoft\Data\Db\FilterHandler\NoneHandler;
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Filter\AndX;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\None;
use Yiisoft\Test\Support\Log\SimpleLogger;

final class QueryDataReaderTest extends TestCase
{
    public function testWithAddedFilterHandlers(): void
    {
        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', ['id' => $columnBuilder::text()])->execute();

        $handler1 = new AllHandler();
        $handler2 = new NoneHandler();
        $handler3 = new AndXHandler();

        $dataReader = new QueryDataReader(
            $db->createQuery()->from('test'),
            filterHandler: new FilterHandler([$handler1]),
        );

        $dataReaderWithAdded = $dataReader->withAddedFilterHandlers($handler2, $handler3);

        $this->assertNotSame($dataReader, $dataReaderWithAdded);

        $dataReaderWithFilters = $dataReaderWithAdded->withFilter(new AndX(new All(), new None()));
        $dataReaderWithFilters->read(); // No errors

        $dataReaderWithUnsupportedFilter = $dataReaderWithAdded->withFilter(new Equals('id', 'test'));
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Operator "' . Equals::class . '" is not supported.');
        $dataReaderWithUnsupportedFilter->read();
    }

    public function testWithAddedFilterHandlersWithIncorrectHandler(): void
    {
        $dataReader = new QueryDataReader(
            TestHelper::createSqliteConnection()->createQuery(),
        );

        $iterableHandler = new \Yiisoft\Data\Reader\Iterable\FilterHandler\AllHandler();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Filter handler must implement "' . QueryFilterHandlerInterface::class . '".');
        $dataReader->withAddedFilterHandlers($iterableHandler);
    }

    public function testGetIteratorAfterRead(): void
    {
        $data = [['id' => '1'], ['id' => '2']];

        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', ['id' => $columnBuilder::text()])->execute();
        $db->createCommand()->insertBatch('test', $data)->execute();

        $logger = new SimpleLogger();
        $db->setLogger($logger);

        $dataReader = new QueryDataReader($db->createQuery()->from('test'));

        $readResult = $dataReader->read();
        $read2Result = $dataReader->read();
        $getIteratorResult = iterator_to_array($dataReader->getIterator());

        $this->assertCount(1, $logger->getMessages()); // Only one query should be logged
        $this->assertSame($data, $readResult);
        $this->assertSame($data, $read2Result);
        $this->assertSame($data, $getIteratorResult);
    }

    public function testBatchReading(): void
    {
        $data = [['id' => '1'], ['id' => '2']];

        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', ['id' => $columnBuilder::text()])->execute();
        $db->createCommand()->insertBatch('test', $data)->execute();

        $dataReader = new QueryDataReader(
            $db->createQuery()->from('test'),
            batchSize: 1,
        );

        $results = [];
        foreach ($dataReader->getIterator() as $item) {
            $results[] = $item;
        }
        $result = array_merge(...$results);

        $this->assertSame($data, $result);
    }

    public function testCountInCommonCaseAfterRead(): void
    {
        $data = [['id' => '1'], ['id' => '2']];

        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', ['id' => $columnBuilder::text()])->execute();
        $db->createCommand()->insertBatch('test', $data)->execute();

        $logger = new SimpleLogger();
        $db->setLogger($logger);

        $dataReader = new QueryDataReader($db->createQuery()->from('test'));

        $result = $dataReader->read();
        $count = $dataReader->count();

        $this->assertCount(1, $logger->getMessages()); // Only one query should be logged
        $this->assertSame($data, $result);
        $this->assertSame(2, $count);
    }

    public function testWithLimitInvalidValue(): void
    {
        $dataReader = new QueryDataReader(
            TestHelper::createSqliteConnection()->createQuery(),
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$limit must not be less than 0.');
        $dataReader->withLimit(-1);
    }

    public function testCustomCountParam(): void
    {
        $data = [['id' => 1], ['id' => 2], ['id' => 1]];

        $db = TestHelper::createSqliteConnection();
        $columnBuilder = $db->getColumnBuilderClass();
        $db->createCommand()->createTable('test', ['id' => $columnBuilder::integer()])->execute();
        $db->createCommand()->insertBatch('test', $data)->execute();

        $dataReader = new QueryDataReader(
            $db->createQuery()->from('test'),
        );

        $newDataReader = $dataReader->withCountParam('DISTINCT id');
        $count = $newDataReader->count();

        $this->assertNotSame($newDataReader, $dataReader);
        $this->assertSame(2, $count);
    }

    public function testDoNotCloneWithCountParamWithSameValue(): void
    {
        $dataReader = new QueryDataReader(
            TestHelper::createSqliteConnection()->createQuery(),
        );

        $dataReader1 = $dataReader->withCountParam('DISTINCT id');
        $dataReader2 = $dataReader1->withCountParam('DISTINCT id');

        $this->assertSame($dataReader1, $dataReader2);
    }

    public function testWithBatchSizeInvalidValue(): void
    {
        $dataReader = new QueryDataReader(
            TestHelper::createSqliteConnection()->createQuery(),
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$batchSize cannot be less than 1.');
        $dataReader->withBatchSize(0);
    }
}
