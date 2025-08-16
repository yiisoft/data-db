<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Between as DbBetweenCondition;

final class BetweenFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Between::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var Between $filter */

        return new DbBetweenCondition($filter->field, $filter->minValue, $filter->maxValue);
    }
}
