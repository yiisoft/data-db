<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\FilterHandler;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\EqualsExpression;
use Yiisoft\Data\Db\FilterHandler\EqualsExpressionHandler;
use Yiisoft\Data\Db\Tests\TestHelper;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\QueryBuilder\Condition\Equals as DbEqualsCondition;

final class EqualsExpressionHandlerTest extends TestCase
{
    public function testBase(): void
    {
        $expression = new Expression('NOW()');
        $filter = new EqualsExpression('created_at', $expression);
        $handler = new EqualsExpressionHandler();

        /** @var DbEqualsCondition $condition */
        $condition = $handler->getCondition($filter, TestHelper::createContext());

        $this->assertSame(EqualsExpression::class, $handler->getFilterClass());
        $this->assertInstanceOf(DbEqualsCondition::class, $condition);
        $this->assertSame('created_at', $condition->column);
        $this->assertSame($expression, $condition->value);
    }
}
