<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Processor\PgSql;

use Yiisoft\Data\Db\Filter\PgSql\ILike as ILikeFilter;
use Yiisoft\Data\Db\Processor\CompareProcessor;

class ILike extends CompareProcessor
{
    public function getOperator(): string
    {
        return ILikeFilter::getOperator();
    }
}
