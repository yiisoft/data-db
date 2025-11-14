<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Filter\Not;
use Yiisoft\Db\QueryBuilder\Condition\Not as DbNotCondition;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

final class NotHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Not::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var Not $filter */

        return new DbNotCondition(
            $context->handleFilter($filter->filter),
        );
    }
}
