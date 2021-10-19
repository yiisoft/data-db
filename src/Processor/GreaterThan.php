<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\GreaterThan as FilterGreaterThan;

class GreaterThan extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterGreaterThan::getOperator();
    }
}
