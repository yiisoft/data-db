<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\FilterHandler;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\FilterHandler\NoneFilterHandler;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Data\Reader\Filter\None;
use Yiisoft\Db\QueryBuilder\Condition\None as DbNoneCondition;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

final class NoneFilterHandlerTest extends TestCase
{
    public function testBase(): void
    {
        $filter = new None();
        $handler = new NoneFilterHandler();

        /** @var DbNoneCondition $condition */
        $condition = $handler->getCondition($filter, TestHelper::createContext());

        assertSame(None::class, $handler->getFilterClass());
        assertInstanceOf(DbNoneCondition::class, $condition);
    }
}
