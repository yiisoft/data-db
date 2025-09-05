<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\Value\DateTimeValue;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\LessThanOrEqual as DbLessThanOrEqual;

final class LessThanOrEqualHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return LessThanOrEqual::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var LessThanOrEqual $filter */

        $value = $filter->value instanceof DateTimeInterface
            ? new DateTimeValue($filter->value)
            : $filter->value;

        return new DbLessThanOrEqual($context->mapField($filter->field), $value);
    }
}
