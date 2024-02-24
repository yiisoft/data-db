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

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var Equals $filter */

        return new Condition(
            ['=', $filter->field, $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
