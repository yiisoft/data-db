<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\PgSql;

use Yiisoft\Data\Db\PgSql\Processor\ArrayContains;
use Yiisoft\Data\Db\PgSql\Processor\RangeContains;
use Yiisoft\Data\Db\PgSql\Processor\TsVector;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Db\Query\Query;

final class PgSqlQueryDataReader extends QueryDataReader
{
    public function __construct(Query $query)
    {
        parent::__construct($query);

        $this->filterProcessors = $this->withFilterProcessors(
            new ArrayContains(),
            new RangeContains(),
            new TsVector()
        )->filterProcessors;
    }
}
