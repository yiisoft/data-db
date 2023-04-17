<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Db\Filter\Like;

final class LikeHandler extends CompareHandler
{
    public function getOperator(): string
    {
        return Like::getOperator();
    }
}
