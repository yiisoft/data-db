<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Equals;

final class EqualsHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return Equals::getOperator();
    }
}
