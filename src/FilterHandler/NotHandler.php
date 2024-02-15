<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Not;

final class NotHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return Not::getOperator();
    }
}
