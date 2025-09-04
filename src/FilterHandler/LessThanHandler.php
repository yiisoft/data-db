<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use Yiisoft\Data\Reader\Filter\LessThan;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\Value\DateTimeValue;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\LessThan as DbLessThan;

final class LessThanHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return LessThan::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var LessThan $filter */

        $value = $filter->value instanceof DateTimeInterface
            ? new DateTimeValue($filter->value)
            : $filter->value;

        return new DbLessThan($context->mapField($filter->field), $value);
    }
}
