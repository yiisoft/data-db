<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Filter\Not;

final class NotHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return Not::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof Not) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        $subCondition = $context->handleFilter($filter->filter);
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
