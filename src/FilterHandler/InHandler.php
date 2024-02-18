<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Reader\Filter\In;

final class InHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return In::getOperator();
    }

    public function getCondition(array $operands, Context $context): ?array
    {
        if (
            array_keys($operands) !== [0, 1]
            || !is_string($operands[0])
            || !is_array($operands[1])
        ) {
            throw new LogicException('Incorrect criteria for the "in" operator.');
        }
        return ['IN', $operands[0], $operands[1]];
    }
}
