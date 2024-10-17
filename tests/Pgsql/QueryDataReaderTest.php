<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql;

use Yiisoft\Data\Db\Tests\Base\BaseQueryDataReaderTestCase;

final class QueryDataReaderTest extends BaseQueryDataReaderTestCase
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
