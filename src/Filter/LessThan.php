<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\LessThan as FilterLessThan;

final class LessThan extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterLessThan::getOperator();
    }
}
