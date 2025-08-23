<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\Value\DateTimeValue;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\GreaterThan as DbGreaterThan;

final class GreaterThanFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return GreaterThan::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var GreaterThan $filter */

        $value = $filter->value instanceof DateTimeInterface
            ? new DateTimeValue($filter->value)
            : $filter->value;

        return new DbGreaterThan($filter->field, $value);
    }
}
