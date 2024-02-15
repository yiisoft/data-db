<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\GreaterThan;

final class GreaterThanHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return GreaterThan::getOperator();
    }
}
