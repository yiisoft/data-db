<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Data\Reader\Iterable\FilterHandler\NoneHandler;

final class QueryDataReaderTest extends TestCase
{
    public function testWithFilterHandlersThrowsExceptionForIncorrectHandler(): void
    {
        $dataReader = new QueryDataReader(
            TestHelper::createSqliteConnection()->createQuery(),
        );

        $iterableHandler = new NoneHandler();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Filter handler must implement "' . QueryFilterHandlerInterface::class . '".');
        $dataReader->withAddedFilterHandlers($iterableHandler);
    }
}
