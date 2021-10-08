<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\LessThanOrEqual as FilterLessThanOrEqual;

final class LessThanOrEqual extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterLessThanOrEqual::getOperator();
    }
}
