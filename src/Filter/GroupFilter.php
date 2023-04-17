<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\FilterInterface;
use function count;

abstract class GroupFilter implements FilterInterface
{
    protected array $filters;

    public function __construct(FilterInterface ...$filters)
    {
        $this->filters = $filters;
    }

    public function toCriteriaArray(): array
    {
        $array = [static::getOperator()];

        foreach ($this->filters as $filter) {
            $arr = $filter->toCriteriaArray();

            if ($arr !== []) {
                $array[] = $arr;
            }
        }

        return count($array) > 1 ? $array : [];
    }
}
