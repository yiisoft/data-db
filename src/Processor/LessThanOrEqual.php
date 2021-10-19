<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\LessThanOrEqual as FilterLessThanOrEqual;

class LessThanOrEqual extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterLessThanOrEqual::getOperator();
    }
}
