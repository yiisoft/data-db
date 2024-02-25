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

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var GreaterThan $filter */

        return new Condition(
            ['>', $filter->getField(), $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
