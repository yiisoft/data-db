<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\Value\DateTimeValue;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;
use Yiisoft\Db\QueryBuilder\Condition\Between as DbBetweenCondition;

final class BetweenHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Between::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ConditionInterface
    {
        /** @var Between $filter */

        return new DbBetweenCondition(
            $filter->field,
            $this->prepareValue($filter->minValue),
            $this->prepareValue($filter->maxValue),
        );
    }

    private function prepareValue(bool|DateTimeInterface|float|int|string $value): mixed
    {
        return $value instanceof DateTimeInterface
            ? new DateTimeValue($value)
            : $value;
    }
}
