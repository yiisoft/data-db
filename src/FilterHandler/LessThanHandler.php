<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\LessThan;

final class LessThanHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return LessThan::getOperator();
    }
}
