<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\ILike as ILikeFilter;
use Yiisoft\Data\Db\Processor\CompareProcessor;

class ILike extends CompareProcessor
{
    public function getOperator(): string
    {
        return ILikeFilter::getOperator();
    }
}
