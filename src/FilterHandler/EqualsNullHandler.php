<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\EqualsNull;

final class EqualsNullHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return EqualsNull::getOperator();
    }
}
