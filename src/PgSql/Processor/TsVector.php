<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\PgSql\Processor;

use Yiisoft\Data\Db\PgSql\Filter\TsVector as TsVectorFilter;
use Yiisoft\Data\Db\Processor\CompareProcessor;

final class TsVector extends CompareProcessor
{
    public function getOperator(): string
    {
        return TsVectorFilter::getOperator();
    }
}
