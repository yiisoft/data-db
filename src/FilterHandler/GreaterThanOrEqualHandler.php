<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Reader\FilterInterface;

final class GreaterThanOrEqualHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return GreaterThanOrEqual::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof GreaterThanOrEqual) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(
            ['>=', $filter->field, $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
