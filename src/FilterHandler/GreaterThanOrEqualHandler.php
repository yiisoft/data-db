<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\Value\DateTimeValue;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\GreaterThanOrEqual as DbGreaterThanOrEqual;

final class GreaterThanOrEqualHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return GreaterThanOrEqual::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var GreaterThanOrEqual $filter */

        $value = $filter->value instanceof DateTimeInterface
            ? new DateTimeValue($filter->value)
            : $filter->value;

        return new DbGreaterThanOrEqual($filter->field, $value);
    }
}
