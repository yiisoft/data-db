<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Filter;

use Yiisoft\Data\Reader\Filter\Equals as DataEquals;

final class Equals extends Compare
{
    public static function getOperator(): string
    {
        return DataEquals::getOperator();
    }
}
