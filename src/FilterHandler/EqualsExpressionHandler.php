<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\EqualsExpression;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Equals as DbEqualsCondition;

final class EqualsExpressionHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return EqualsExpression::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var EqualsExpression $filter */

        return new DbEqualsCondition(
            $context->mapField($filter->field),
            $filter->expression,
        );
    }
}
