<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\FilterInterface;

final class BetweenFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Between::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var Between $filter */

        return new Condition([
            'BETWEEN',
            $filter->field,
            $context->normalizeValueToScalar($filter->minValue),
            $context->normalizeValueToScalar($filter->maxValue),
        ]);
    }
}
