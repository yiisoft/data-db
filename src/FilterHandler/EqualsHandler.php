<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Equals;

final class EqualsHandler extends BaseHandler
{
    public function getOperator(): string
    {
        return Equals::getOperator();
    }
}
