<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Equals as FilterEquals;

final class Equals extends CompareFilter
{
    public static function getOperator(): string
    {
        return FilterEquals::getOperator();
    }
}
