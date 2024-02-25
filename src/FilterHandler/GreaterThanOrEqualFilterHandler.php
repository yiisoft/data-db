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

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var GreaterThanOrEqual $filter */

        return new Condition(
            ['>=', $filter->getField(), $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
