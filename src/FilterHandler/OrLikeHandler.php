<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\OrLike;

final class OrLikeHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return OrLike::getOperator();
    }
}
