<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\GreaterThan as FilterGreaterThan;

final class GreaterThan extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterGreaterThan::getOperator();
    }
}
