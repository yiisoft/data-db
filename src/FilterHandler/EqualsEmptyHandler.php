<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\EqualsEmpty;

final class EqualsEmptyHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return EqualsEmpty::getOperator();
    }
}
