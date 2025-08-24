<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\FilterHandler;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\Exists;
use Yiisoft\Data\Db\FilterHandler\ExistsFilterHandler;
use Yiisoft\Data\Db\Tests\TestHelper;

use Yiisoft\Db\QueryBuilder\Condition\Exists as DbExistsCondition;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

final class ExistsFilterHandlerTest extends TestCase
{
    public function testBase(): void
    {
        $query = TestHelper::createSqliteConnection()->createQuery();
        $filter = new Exists($query);
        $handler = new ExistsFilterHandler();

        /** @var DbExistsCondition $condition */
        $condition = $handler->getCondition($filter, TestHelper::createContext());

        assertSame(Exists::class, $handler->getFilterClass());
        assertInstanceOf(DbExistsCondition::class, $condition);
        assertSame($query, $condition->query);
    }
}
