<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\Between as FilterBetween;

class Between extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterBetween::getOperator();
    }
}
