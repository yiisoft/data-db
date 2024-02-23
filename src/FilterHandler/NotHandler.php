<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use LogicException;
use Yiisoft\Data\Reader\Filter\Not;

final class NotHandler implements QueryHandlerInterface
{
    public function getOperator(): string
    {
        return Not::getOperator();
    }

    public function getCondition(array $operands, Context $context): ?Condition
    {
        if (
            array_keys($operands) !== [0]
            || !is_array($operands[0])
        ) {
            throw new LogicException('Incorrect criteria for the "not" operator.');
        }
        $subCondition = $context->handleCriteria($operands[0]);
        if ($subCondition === null) {
            return null;
        }

        $body = $subCondition->body;
        $params = $subCondition->params;

        if (isset($body[0]) && is_string($body[0])) {
            $convert = [
                'IS' => 'IS NOT',
                'IN' => 'NOT IN',
                'EXISTS' => 'NOT EXISTS',
                'BETWEEN' => 'NOT BETWEEN',
                'LIKE' => 'NOT LIKE',
                'ILIKE' => 'NOT ILIKE',
                '>' => '<=',
                '>=' => '<',
                '<' => '>=',
                '<=' => '>',
                '=' => '!=',
            ];
            $operator = strtoupper($body[0]);
            if (isset($convert[$operator])) {
                $body[0] = $convert[$operator];
                return new Condition($body, $params);
            }
        }

        return new Condition(['NOT', $body], $params);
    }
}
