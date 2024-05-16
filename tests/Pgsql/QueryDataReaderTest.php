<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql;

final class QueryDataReaderTest extends \Yiisoft\Data\Db\Tests\Base\QueryDataReaderTest
{
    use DatabaseTrait;

    public static function dataOffset(): array
    {
        return [
            [
                'SELECT * FROM "customer" OFFSET 2',
            ],
        ];
    }
}
