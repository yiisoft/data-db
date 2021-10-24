<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Not as FilterNot;
use Yiisoft\Data\Reader\Filter\FilterInterface;

final class Not implements FilterInterface
{
    private FilterInterface $filter;

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    public static function getOperator(): string
    {
        return FilterNot::getOperator();
    }

    public function toArray(): array
    {
        $array = $this->filter->toArray();
        $count = count($array);

        if ($count === 0) {
            return [];
        }

        $value = $array[$count - 1];

        if ($value === null) {
            $column = $array[$count - 2];
            return [self::getOperator(), [$column => null]];
        }

        return [self::getOperator(), $array];
    }
}
