<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\FilterHandler;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\EqualsExpression;
use Yiisoft\Data\Db\FilterHandler\EqualsExpressionFilterHandler;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\QueryBuilder\Condition\Equals as DbEqualsCondition;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertSame;

final class EqualsExpressionFilterHandlerTest extends TestCase
{
    public function testBase(): void
    {
        $expression = new Expression('NOW()');
        $filter = new EqualsExpression('created_at', $expression);
        $handler = new EqualsExpressionFilterHandler();

        /** @var DbEqualsCondition $condition */
        $condition = $handler->getCondition($filter, TestHelper::createContext());

        assertSame(EqualsExpression::class, $handler->getFilterClass());
        assertInstanceOf(DbEqualsCondition::class, $condition);
        assertSame('created_at', $condition->column);
        assertSame($expression, $condition->value);
    }
}
