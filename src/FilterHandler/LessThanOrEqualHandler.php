<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\LessThanOrEqual;

final class LessThanOrEqualHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return LessThanOrEqual::getOperator();
    }
}
