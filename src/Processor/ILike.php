<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\ILike as ILikeFilter;

class ILike extends CompareProcessor
{
    public function getOperator(): string
    {
        return ILikeFilter::getOperator();
    }
}
