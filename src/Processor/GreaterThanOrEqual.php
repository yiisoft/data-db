<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\GreaterThanOrEqual as FilterGreaterThanOrEqual;

class GreaterThanOrEqual extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterGreaterThanOrEqual::getOperator();
    }
}
