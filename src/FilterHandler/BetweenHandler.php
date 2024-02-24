<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use InvalidArgumentException;
use LogicException;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\FilterInterface;

final class BetweenHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return Between::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof Between) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition([
            'BETWEEN',
            $filter->field,
            $context->normalizeValueToScalar($filter->minValue),
            $context->normalizeValueToScalar($filter->maxValue),
        ]);
    }
}
