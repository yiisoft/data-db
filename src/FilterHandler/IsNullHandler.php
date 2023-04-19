<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\IsNull;

final class IsNullHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return IsNull::getOperator();
    }
}
