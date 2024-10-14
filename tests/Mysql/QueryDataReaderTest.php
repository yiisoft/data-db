<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql;

use Yiisoft\Data\Db\Tests\Base\BaseQueryDataReaderTestCase;

final class QueryDataReaderTest extends BaseQueryDataReaderTestCase
{
    use DatabaseTrait;

    public static function dataOffset(): array
    {
        return [
            [
                'SELECT * FROM `customer` LIMIT 2, 18446744073709551615',
            ],
        ];
    }
}
