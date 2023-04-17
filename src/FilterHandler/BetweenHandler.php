<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Between;

final class BetweenHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return Between::getOperator();
    }
}
