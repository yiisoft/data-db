<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Any;

final class AnyHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return Any::getOperator();
    }
}
