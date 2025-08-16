<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\AndX;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\AndX as DbAndXCondition;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

final class AndXFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return AndX::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var AndX $filter */

        return new DbAndXCondition(
            ...array_map(
                static fn(FilterInterface $subFilter) => $context->handleFilter($subFilter),
                $filter->filters,
            ),
        );
    }
}
