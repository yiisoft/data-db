<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql;

use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\Not;

final class DataFilterTest extends \Yiisoft\Data\Db\Tests\Base\DataFilterTest
{
    use DatabaseTrait;

    public static function simpleDataProvider(): array
    {
        $data = parent::simpleDataProvider();
        $data['like'] = [
            new Like('column', 'foo'),
            "`column` LIKE '%foo%'",
        ];

        return $data;
    }

    public static function notDataProvider(): array
    {
        $data = parent::simpleDataProvider();
        $data['like'] = [
            new Not(new Like('column', 'foo')),
            "`column` NOT LIKE '%foo%'",
        ];

        return $data;
    }
}
