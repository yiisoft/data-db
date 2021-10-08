<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\Exists as FilterExists;

final class Exists extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterExists::getOperator();
    }
}
