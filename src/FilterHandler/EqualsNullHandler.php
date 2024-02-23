<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Reader\Filter\EqualsNull;

final class EqualsNullHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return EqualsNull::getOperator();
    }

    public function getCondition(array $operands, Context $context): ?Condition
    {
        if (
            array_keys($operands) !== [0]
            || !is_string($operands[0])
        ) {
            throw new LogicException('Incorrect criteria for the "empty" operator.');
        }
        return new Condition(['IS', $operands[0], null]);
    }
}
