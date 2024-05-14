<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql;

final class DataFilterTest extends \Yiisoft\Data\Db\Tests\Base\DataFilterTest
{
    use DatabaseTrait;

    public static function simpleDataProvider(): array
    {
        $data = parent::simpleDataProvider();
        $data['like'][1] = "`column` LIKE '%foo%'";

        return $data;
    }

    public static function notDataProvider(): array
    {
        $data = parent::notDataProvider();
        $data['like'][1] = "`column` NOT LIKE '%foo%'";

        return $data;
    }
}
