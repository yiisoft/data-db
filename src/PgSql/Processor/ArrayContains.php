<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\PgSql\Processor;

use Yiisoft\Data\Db\PgSql\Filter\ArrayContains as ArrayContainsFilter;
use Yiisoft\Data\Db\Processor\CompareProcessor;

final class ArrayContains extends CompareProcessor
{
    public function getOperator(): string
    {
        return ArrayContainsFilter::getOperator();
    }
}
