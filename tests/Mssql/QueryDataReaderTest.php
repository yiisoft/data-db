<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mssql;

final class QueryDataReaderTest extends \Yiisoft\Data\Db\Tests\Base\QueryDataReaderTest
{
    use DatabaseTrait;

    public static function dataOffset(): array
    {
        return [
            [
                'SELECT * FROM [customer] ORDER BY (SELECT NULL) OFFSET 2 ROWS',
            ],
        ];
    }

    public static function dataLimit(): array
    {
        return [
            [
                'SELECT * FROM [customer] ORDER BY (SELECT NULL) OFFSET 1 ROWS FETCH NEXT 1 ROWS ONLY',
            ],
        ];
    }
}
