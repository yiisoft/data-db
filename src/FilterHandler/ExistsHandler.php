<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Exists;

final class ExistsHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return Exists::getOperator();
    }
}
