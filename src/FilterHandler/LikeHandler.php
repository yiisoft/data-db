<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Reader\Filter\Like;

final class LikeHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return Like::getOperator();
    }

    public function getCondition(string $operator, array $operands, CriteriaHandler $criteriaHandler): ?array
    {
        if (
            array_keys($operands) !== [0, 1]
            || !is_string($operands[0])
            || !is_string($operands[1])
        ) {
            throw new LogicException('Incorrect criteria for the "like" operator.');
        }
        return ['LIKE', $operands[0], $operands[1]];
    }
}
