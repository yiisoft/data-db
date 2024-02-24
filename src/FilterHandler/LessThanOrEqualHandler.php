<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\FilterInterface;

final class LessThanOrEqualHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return LessThanOrEqual::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof LessThanOrEqual) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(
            ['<=', $filter->field, $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
