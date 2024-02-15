<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;

abstract class CompareHandler implements QueryHandlerInterface
{
    public function getCondition(array $operands, CriteriaHandler $criteriaHandler): ?array
    {
        if (
            array_keys($operands) !== [0, 1]
            || !is_string($operands[0])
            || (
                !is_string($operands[1])
                && !(is_scalar($operands[1]) || $operands[1] instanceof DateTimeInterface)
            )
        ) {
            throw new LogicException(sprintf('Incorrect criteria for the "%s" operator.', $this->getOperator()));
        }

        $value = $operands[1] instanceof DateTimeInterface
            ? $operands[1]->format('Y-m-d H:i:s')
            : $operands[1];

        return [$this->getOperator(), $operands[0], $value];
    }
}
