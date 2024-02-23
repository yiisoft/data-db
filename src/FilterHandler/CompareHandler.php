<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use DateTimeInterface;
use LogicException;
use Yiisoft\Data\Db\Filter\Compare;

abstract class CompareHandler implements QueryHandlerInterface
{
    public function getCondition(array $operands, Context $context): ?Condition
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

        if (array_key_exists(2, $operands)) {
            $dbFilter = $operands[2];
            if (!$dbFilter instanceof Compare) {
                throw new LogicException(sprintf('Incorrect criteria for the "%s" operator.', $this->getOperator()));
            }
            $params = $dbFilter->getParams();
        } else {
            $params = [];
        }

        return new Condition(
            [$this->getOperator(), $operands[0], $context->normalizeValueToScalar($operands[1])],
            $params,
        );
    }
}
