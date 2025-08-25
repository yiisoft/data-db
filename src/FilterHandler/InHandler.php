<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\In as DbInCondition;

final class InHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return In::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var In $filter */

        return new DbInCondition($filter->field, $filter->values);
    }
}
