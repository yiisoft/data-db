<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

final class DataReaderTest extends \Yiisoft\Data\Db\Tests\Base\DataReaderTest
{
    use DatabaseTrait;

    public static function dataOffset(): array
    {
        return [
            [
                'SELECT * FROM `customer` LIMIT 9223372036854775807 OFFSET 2',
            ],
        ];
    }
}
