<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Data\Db\FilterHandler\BetweenHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\InHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\LikeHandler;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\CustomerDataReader;
use Yiisoft\Data\Db\Tests\Support\CustomerDTO;
use Yiisoft\Data\Db\Tests\Support\CustomerQuery;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\Filter\LessThan;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

final class DataReaderTest extends TestCase
{
    use TestTrait;

    public function testDataReader(): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');
        $dataReader = new QueryDataReader($query);

        $this->assertSame(
            $query->createCommand()->getRawSql(),
            $dataReader->getPreparedQuery()->createCommand()->getRawSql()
        );
    }

    public function testOffset(): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withOffset(2);
        $query->offset(2);

        $actual = $dataReader->getPreparedQuery()->createCommand()->getRawSql();
        $expected = $query->createCommand()->getRawSql();

        self::assertSame($expected, $actual);
        self::assertStringEndsWith('OFFSET 2', $actual);
    }

    public function testLimit(): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');
        $dataReader = (new QueryDataReader($query))
            ->withOffset(1)
            ->withLimit(1);
        $query
            ->offset(1)
            ->limit(1);

        $actual = $dataReader->getPreparedQuery()->createCommand()->getRawSql();
        $expected = $query->createCommand()->getRawSql();

        self::assertSame($expected, $actual);
        self::assertStringEndsWith('LIMIT 1 OFFSET 1', $actual);
    }

    public function sortDataProvider(): array
    {
        return [
            [
                Sort::only([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,email'),
                '[name] DESC, [email]',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withOrderString('-name,-email'),
                '[name] DESC, [email] DESC',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withoutDefaultSorting()
                ->withOrderString('-email'),
                '[email] DESC',
            ],
            [
                Sort::any([
                    'name',
                    'email',
                ])
                ->withOrderString('-email'),
                '[email] DESC, [name]',
            ],
            [
                Sort::any([
                    'name' => [
                        'asc' => [
                            new Expression('[[name]] ASC NULLS FIRST'),
                        ],
                        'desc' => [
                            new Expression('[[name]] DESC NULLS LAST'),
                        ],
                    ],
                ])
                ->withOrderString('-name'),
                '[name] DESC NULLS LAST',
            ],
        ];
    }

    /**
     * @dataProvider sortDataProvider
     */
    public function testSort(Sort $sort, string $expected): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');

        $dataReader = (new QueryDataReader($query))
            ->withSort($sort);

        self::assertStringEndsWith(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql()
        );
    }

    public static function handlerDataProvider(): array
    {
        return [
            [
                new Equals('equals', 1),
                EqualsHandler::class,
            ],
            [
                new Between('column', 100, 300),
                BetweenHandler::class,
            ],
            [
                new GreaterThan('column', 1000),
                GreaterThanHandler::class,
            ],
            [
                new GreaterThanOrEqual('column', 3.5),
                GreaterThanOrEqualHandler::class,
            ],
            [
                new LessThan('column', 10.7),
                LessThanHandler::class,
            ],
            [
                new LessThanOrEqual('column', 100),
                LessThanOrEqualHandler::class,
            ],
            [
                new In('column', [10, 20, 30]),
                InHandler::class,
            ],
            [
                new Like('column', 'foo'),
                LikeHandler::class,
            ],
        ];
    }

    public function testCount(): void
    {
        $query = new CustomerQuery($this->getConnection());
        $dataReader = (new CustomerDataReader($query));

        self::assertEquals($query->count(), $dataReader->count());
    }

    /**
     * @dataProvider handlerDataProvider
     */
    public function testHandlerByOperation(FilterInterface $filter, string $handler): void
    {
        $db = $this->getConnection();

        $query = (new Query($db))
            ->from('customer');

        $dataReader = new QueryDataReader($query);
        $filterHandler = $dataReader->getHandlerByOperation($filter);
        $filterOperatorHandler = $dataReader->getHandlerByOperation($filter::getOperator());

        self::assertInstanceOf($handler, $filterHandler);
        self::assertInstanceOf($handler, $filterOperatorHandler);
    }

    public function testDtoCreateItem(): void
    {
        $query = new CustomerQuery($this->getConnection());
        $dataReader = (new CustomerDataReader($query))
            ->withBatchSize(null);


        self::assertInstanceOf(CustomerDTO::class, $dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            self::assertInstanceOf(CustomerDTO::class, $row);
        }
    }

    public function testObjectCreateItem(): void
    {
        $query = (new CustomerQuery($this->getConnection()))
            ->asObject(true);
        $dataReader = (new QueryDataReader($query))
            ->withBatchSize(null);

        self::assertInstanceOf(stdClass::class, $dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            self::assertInstanceOf(stdClass::class, $row);
        }
    }

    public function testArrayCreateItem(): void
    {
        $query = new CustomerQuery($this->getConnection());
        $dataReader = (new QueryDataReader($query))
            ->withBatchSize(null);

        self::assertIsArray($dataReader->readOne());

        foreach ($dataReader->read() as $row) {
            self::assertIsArray($row);
        }
    }
}
