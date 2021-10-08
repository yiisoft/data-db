<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\In as FilterIn;

class In extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterIn::getOperator();
    }
}
