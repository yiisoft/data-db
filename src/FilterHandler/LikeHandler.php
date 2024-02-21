<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\FilterHandler;

use Yiisoft\Data\Reader\Filter\Like;

final class LikeHandler extends AbstractHandler
{
    public function getOperator(): string
    {
        return Like::getOperator();
    }
}
