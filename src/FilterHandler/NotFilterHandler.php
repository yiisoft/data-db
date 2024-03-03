<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Filter\Not;

final class NotFilterHandler implements QueryFilterHandlerInterface
{
    public function getFilterClass(): string
    {
        return Not::class;
    }

    public function getCriteria(FilterInterface $filter, Context $context): ?Criteria
    {
        /** @var Not $filter */

        $subCriteria = $context->handleFilter($filter->getFilter());
        if ($subCriteria === null) {
            return null;
        }

        $condition = $subCriteria->condition;
        $params = $subCriteria->params;

        if (isset($condition[0]) && is_string($condition[0])) {
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
            $operator = strtoupper($condition[0]);
            if (isset($convert[$operator])) {
                $condition[0] = $convert[$operator];
                return new Criteria($condition, $params);
            }
        }

        return new Criteria(['NOT', $condition], $params);
    }
}
