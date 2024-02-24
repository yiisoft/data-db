<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\FilterInterface;

final class EqualsHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return Equals::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof Equals) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(
            ['=', $filter->field, $context->normalizeValueToScalar($filter->getValue())],
        );
    }
}
