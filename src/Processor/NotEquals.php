<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\NotEquals as NotEqualsFilter;

final class NotEquals extends CompareProcessor
{
    public function getOperator(): string
    {
        return NotEqualsFilter::getOperator();
    }
}
