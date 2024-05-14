<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql;

final class DataReaderTest extends \Yiisoft\Data\Db\Tests\Base\DataReaderTest
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
