<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\FilterInterface;

final class EqualsFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Equals::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var Equals $filter */

        return new Criteria(
            ['=', $filter->getField(), $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
