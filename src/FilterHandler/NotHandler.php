<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Not;

final class NotHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return Not::getOperator();
    }
}
