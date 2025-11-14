<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\QueryDataReader\StringCount;

use RuntimeException;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Tests\TestCase;

final class StringCountTest extends TestCase
{
    public function testBase(): void
    {
        $dataReader = new QueryDataReader(new QueryStub());

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Number of records is too large to fit into a PHP integer. Got 9223372036854775808.',
        );
        $dataReader->count();
    }
}
