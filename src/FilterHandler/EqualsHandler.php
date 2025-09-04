<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\Value\DateTimeValue;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Equals as DbEqualsCondition;

final class EqualsHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Equals::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var Equals $filter */

        $value = $filter->value instanceof DateTimeInterface
            ? new DateTimeValue($filter->value)
            : $filter->value;

        return new DbEqualsCondition($context->mapField($filter->field), $value);
    }
}
