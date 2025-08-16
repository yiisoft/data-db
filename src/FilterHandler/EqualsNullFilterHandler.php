<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\EqualsNull;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Equals as DbEquals;

final class EqualsNullFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return EqualsNull::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var EqualsNull $filter */

        return new DbEquals($filter->field, null);
    }
}
