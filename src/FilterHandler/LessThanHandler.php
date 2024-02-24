<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\LessThan;
use Yiisoft\Data\Reader\FilterInterface;

final class LessThanHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return LessThan::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof LessThan) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(
            ['<', $filter->field, $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
