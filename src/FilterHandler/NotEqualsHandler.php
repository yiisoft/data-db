<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\NotEquals;

final class NotEqualsHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return NotEquals::getOperator();
    }
}
