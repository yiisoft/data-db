<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Reader\FilterInterface;

final class GreaterThanHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return GreaterThan::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof GreaterThan) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(
            ['>', $filter->field, $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
