<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Oracle\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithLikeTestCase;
use Yiisoft\Data\Db\Tests\Oracle\DatabaseTrait;

final class ReaderWithLikeTest extends BaseReaderWithLikeTestCase
{
    use DatabaseTrait;

    public static function dataWithReader(): array
    {
        $data = parent::dataWithReader();
        $data['search: contains, same case, case sensitive: true'] = ['email', 'ed@be', true, [2]];
        $data['search: contains, different case, case sensitive: true'] = ['email', 'SEED@', true, []];
        // TODO: Remove after changes in yiisoft/db
        unset(
            $data['search: contains, different case, case sensitive: null'],
            $data['search: contains, different case, case sensitive: false'],
        );

        return $data;
    }
}
