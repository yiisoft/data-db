<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Any as FilterAny;

final class Any extends GroupFilter
{
    public static function getOperator(): string
    {
        return FilterAny::getOperator();
    }
}
