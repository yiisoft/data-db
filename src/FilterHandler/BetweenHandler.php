<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Reader\Filter\Between;

final class BetweenHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return Between::getOperator();
    }

    public function getCondition(string $operator, array $operands, CriteriaHandler $criteriaHandler): ?array
    {
        if (
            array_keys($operands) !== [0, 1, 2]
            || !is_string($operands[0])
            || !(is_scalar($operands[1]) || $operands[1] instanceof DateTimeInterface)
            || !(is_scalar($operands[2]) || $operands[2] instanceof DateTimeInterface)
        ) {
            throw new LogicException('Incorrect criteria for the "between" operator.');
        }
        $from = $operands[1] instanceof DateTimeInterface
            ? $operands[1]->format('Y-m-d H:i:s')
            : $operands[1];
        $to = $operands[2] instanceof DateTimeInterface
            ? $operands[2]->format('Y-m-d H:i:s')
            : $operands[2];
        return ['BETWEEN', $operands[0], $from, $to];
    }
}
