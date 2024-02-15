<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\All;

final class AllHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return All::getOperator();
    }
}
