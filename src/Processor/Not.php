<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\Not as FilterNot;

final class Not extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterNot::getOperator();
    }
}
