<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithLikeTestCase;
use Yiisoft\Data\Db\Tests\Pgsql\DatabaseTrait;

final class ReaderWithLikeTest extends BaseReaderWithLikeTestCase
{
    use DatabaseTrait;

    public static function dataWithReader(): array
    {
        $data = parent::dataWithReader();
        $data['search: contains, same case, case sensitive: true'] = ['email', 'ed@be', true, [2]];
        $data['search: contains, different case, case sensitive: true'] = ['email', 'SEED@', true, []];

        return $data;
    }
}
