<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\All;

final class AllHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return All::getOperator();
    }
}
