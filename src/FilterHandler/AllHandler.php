<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use InvalidArgumentException;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\FilterInterface;

final class AllHandler implements QueryHandlerInterface
{
    public function getFilterClass(): string
    {
        return All::class;
    }

    public function getCondition(FilterInterface $filter, Context $context): ?Condition
    {
        if (!$filter instanceof All) {
            throw new InvalidArgumentException('Incorrect filter.');
        }

        $filters = $filter->getFilters();
        if (empty($filters)) {
            return null;
        }

        $body = ['AND'];
        $params = [];

        foreach ($filters as $subFilter) {
            $condition = $context->handleFilter($subFilter);
            if ($condition !== null) {
                $body[] = $condition->body;
                $params = array_merge($params, $condition->params);
            }
        }
        return new Condition($body, $params);
    }
}
