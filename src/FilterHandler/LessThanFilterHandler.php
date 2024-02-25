<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\LessThan;
use Yiisoft\Data\Reader\FilterInterface;

final class LessThanFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return LessThan::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var LessThan $filter */

        return new Condition(
            ['<', $filter->getField(), $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
