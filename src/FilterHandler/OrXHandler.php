<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\OrX;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\OrX as DbOrXCondition;

final class OrXHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return OrX::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var OrX $filter */

        return new DbOrXCondition(
            ...array_map(
                $context->handleFilter(...),
                $filter->filters,
            ),
        );
    }
}
