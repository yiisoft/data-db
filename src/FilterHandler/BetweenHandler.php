<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use LogicException;
use Yiisoft\Data\Reader\Filter\Between;

final class BetweenHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return Between::getOperator();
    }

    public function getCondition(array $operands, Context $context): ?Condition
    {
        if (
            array_keys($operands) !== [0, 1, 2]
            || !is_string($operands[0])
            || !(is_scalar($operands[1]) || $operands[1] instanceof DateTimeInterface)
            || !(is_scalar($operands[2]) || $operands[2] instanceof DateTimeInterface)
        ) {
            throw new LogicException('Incorrect criteria for the "between" operator.');
        }
        return new Condition([
            'BETWEEN',
            $operands[0],
            $context->normalizeValueToScalar($operands[1]),
            $context->normalizeValueToScalar($operands[2]),
        ]);
    }
}
