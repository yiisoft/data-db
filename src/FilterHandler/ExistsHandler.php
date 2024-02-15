<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Db\Filter\Exists;
use Yiisoft\Db\Query\QueryInterface;

final class ExistsHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return Exists::getOperator();
    }

    public function getCondition(array $operands, Context $context): ?array
    {
        if (
            array_keys($operands) !== [0]
            || !$operands[0] instanceof QueryInterface
        ) {
            throw new LogicException('Incorrect criteria for the "exists" operator.');
        }

        return ['EXISTS', $operands[0]];
    }
}
