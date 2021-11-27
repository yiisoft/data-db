<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\PgSql\Processor;

use Yiisoft\Data\Db\PgSql\Filter\RangeContains as RangeContainsFilter;
use Yiisoft\Data\Db\Processor\CompareProcessor;

final class RangeContains extends CompareProcessor
{
    public function getOperator(): string
    {
        return RangeContainsFilter::getOperator();
    }
}
