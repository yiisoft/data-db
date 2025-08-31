<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mssql\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithLikeTestCase;
use Yiisoft\Data\Db\Tests\Mssql\DatabaseTrait;

final class ReaderWithLikeTest extends BaseReaderWithLikeTestCase
{
    use DatabaseTrait;

    public static function dataWithReader(): array
    {
        $data = parent::dataWithReader();

        // MSSQL doesn't support case-sensitive "LIKE" conditions
        unset(
            $data['search: contains, same case, case sensitive: true'],
            $data['search: contains, different case, case sensitive: true'],
        );

        return $data;
    }
}
