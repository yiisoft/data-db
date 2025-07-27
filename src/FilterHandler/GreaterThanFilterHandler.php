<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\FilterInterface;

final class GreaterThanFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return GreaterThan::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var GreaterThan $filter */

        return new Criteria(
            ['>', $filter->field, $context->normalizeValueToScalar($filter->value)],
        );
    }
}
