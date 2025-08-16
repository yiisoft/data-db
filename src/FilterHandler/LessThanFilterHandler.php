<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\LessThan;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\LessThan as DbLessThan;

final class LessThanFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return LessThan::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var LessThan $filter */

        return new DbLessThan($filter->field, $filter->value);
    }
}
