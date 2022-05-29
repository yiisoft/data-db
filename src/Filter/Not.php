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

        if ($array === []) {
            return [];
        }

        switch ($array[0]) {
            case IsNull::getOperator():
                $array[0] .= ' ' . self::getOperator();
                break;
            case In::getOperator():
            case Exists::getOperator():
            case Between::getOperator():
                $array[0] = self::getOperator() . ' ' . $array[0];
                break;
            default:
                $array = [self::getOperator(), $array];
        }

        return $array;
    }
}
