<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\QueryDataReader\StringCount;

use Yiisoft\Db\Query\Query;

final class QueryStub extends Query
{
    public function __construct() {}

    public function count(string $sql = '*'): int|string
    {
        return '9223372036854775808';
    }
}
