<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\GreaterThanOrEqual;

final class GreaterThanOrEqualHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return GreaterThanOrEqual::getOperator();
    }
}
