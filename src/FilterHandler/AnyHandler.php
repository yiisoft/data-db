<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Any;

final class AnyHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return Any::getOperator();
    }
}
