<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\FilterHandler;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\Exists;
use Yiisoft\Data\Db\FilterHandler\ExistsHandler;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Db\QueryBuilder\Condition\Exists as DbExistsCondition;

final class ExistsHandlerTest extends TestCase
{
    public function testBase(): void
    {
        $query = TestHelper::createSqliteConnection()->createQuery();
        $filter = new Exists($query);
        $handler = new ExistsHandler();

        /** @var DbExistsCondition $condition */
        $condition = $handler->getCondition($filter, TestHelper::createContext());

        $this->assertSame(Exists::class, $handler->getFilterClass());
        $this->assertInstanceOf(DbExistsCondition::class, $condition);
        $this->assertSame($query, $condition->query);
    }
}
