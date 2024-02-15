<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\GreaterThanOrEqual;

final class GreaterThanOrEqualHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return GreaterThanOrEqual::getOperator();
    }
}
