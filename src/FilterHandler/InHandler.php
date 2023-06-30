<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\In;

final class InHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return In::getOperator();
    }
}
