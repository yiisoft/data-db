<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\FilterInterface;

abstract class GroupFilter implements FilterInterface
{
    protected array $filters;

    public function __construct(FilterInterface ...$filters)
    {
        $this->filters = $filters;
    }

    public function toArray(): array
    {
        $array = [static::getOperator()];

        foreach ($this->filters as $filter) {
            $arr = $filter->toArray();

            if (count($arr)) {
                $array[] = $filter->toArray();
            }
        }

        return count($array) > 1 ? $array : [];
    }
}
