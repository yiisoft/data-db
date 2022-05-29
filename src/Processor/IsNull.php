<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\IsNull as FilterIsNull;

final class IsNull extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterIsNull::getOperator();
    }
}
