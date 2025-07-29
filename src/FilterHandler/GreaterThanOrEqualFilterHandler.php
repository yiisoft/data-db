<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Reader\FilterInterface;

final class GreaterThanOrEqualFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return GreaterThanOrEqual::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var GreaterThanOrEqual $filter */

        return new Criteria(
            ['>=', $filter->field, $context->normalizeValueToScalar($filter->value)],
        );
    }
}
