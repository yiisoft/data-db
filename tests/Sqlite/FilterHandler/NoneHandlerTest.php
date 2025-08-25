<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\FilterHandler;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\FilterHandler\NoneHandler;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Data\Reader\Filter\None;
use Yiisoft\Db\QueryBuilder\Condition\None as DbNoneCondition;

final class NoneHandlerTest extends TestCase
{
    public function testBase(): void
    {
        $filter = new None();
        $handler = new NoneHandler();

        /** @var DbNoneCondition $condition */
        $condition = $handler->getCondition($filter, TestHelper::createContext());

        $this->assertSame(None::class, $handler->getFilterClass());
        $this->assertInstanceOf(DbNoneCondition::class, $condition);
    }
}
