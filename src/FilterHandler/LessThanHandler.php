<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\LessThan;

final class LessThanHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return LessThan::getOperator();
    }
}
