<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use LogicException;
use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\FilterInterface;

final class InHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return In::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof In) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        return new Condition(['IN', $filter->field, $filter->getValues()]);
    }
}
