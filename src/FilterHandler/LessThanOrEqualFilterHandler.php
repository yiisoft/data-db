<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\FilterInterface;

final class LessThanOrEqualFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return LessThanOrEqual::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        /** @var LessThanOrEqual $filter */

        return new Condition(
            ['<=', $filter->field, $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
