<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\Like as FilterLike;

class Like extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterLike::getOperator();
    }
}
