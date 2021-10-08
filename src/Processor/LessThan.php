<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\LessThan as FilterLessThan;

class LessThan extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterLessThan::getOperator();
    }
}
