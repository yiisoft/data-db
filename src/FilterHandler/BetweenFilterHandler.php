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

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var Between $filter */

        return new Criteria([
            'BETWEEN',
            $filter->getField(),
            $context->normalizeValueToScalar($filter->getMinValue()),
            $context->normalizeValueToScalar($filter->getMaxValue()),
        ]);
    }
}
