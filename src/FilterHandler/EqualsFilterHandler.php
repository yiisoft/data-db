<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Equals as DbEqualsCondition;

final class EqualsFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Equals::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var Equals $filter */

        return new DbEqualsCondition($filter->field, $filter->value);
    }
}
