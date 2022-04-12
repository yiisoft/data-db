<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor;

use Yiisoft\Data\Db\Filter\Equals as FilterEquals;

final class Equals extends CompareProcessor
{
    public function getOperator(): string
    {
        return FilterEquals::getOperator();
    }
}
