<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual as FilterGreaterThanOrEqual;

final class GreaterThanOrEqual extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterGreaterThanOrEqual::getOperator();
    }
}
