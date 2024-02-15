<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Reader\Filter\EqualsEmpty;

final class EqualsEmptyHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return EqualsEmpty::getOperator();
    }

    public function getCondition(array $operands, CriteriaHandler $criteriaHandler): ?array
    {
        if (
            array_keys($operands) !== [0]
            || !is_string($operands[0])
        ) {
            throw new LogicException('Incorrect criteria for the "empty" operator.');
        }
        return ['OR', ['IS', $operands[0], null], ['=', $operands[0], '']];
    }
}
