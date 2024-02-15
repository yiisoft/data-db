<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Reader\Filter\Equals;

final class EqualsHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return Equals::getOperator();
    }

    public function getCondition(array $operands, CriteriaHandler $criteriaHandler): ?array
    {
        if (
            array_keys($operands) !== [0, 1]
            || !is_string($operands[0])
            || (
                !is_string($operands[1])
                && !(is_scalar($operands[1]) || is_null($operands[1]) || $operands[1] instanceof DateTimeInterface)
            )
        ) {
            throw new LogicException('Incorrect criteria for the "=" operator.');
        }

        if ($operands[1] === null) {
            return ['IS NULL', $operands[0]];
        }

        $value = $operands[1] instanceof DateTimeInterface
            ? $operands[1]->format('Y-m-d H:i:s')
            : $operands[1];

        return ['=', $operands[0], $value];
    }
}
