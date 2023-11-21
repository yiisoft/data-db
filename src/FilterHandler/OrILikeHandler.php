<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\OrILike;

final class OrILikeHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return OrILike::getOperator();
    }
}
