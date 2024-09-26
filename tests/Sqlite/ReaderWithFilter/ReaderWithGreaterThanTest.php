<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithGreaterThanTestCase;
use Yiisoft\Data\Db\Tests\Sqlite\DatabaseTrait;

final class ReaderWithGreaterThanTest extends BaseReaderWithGreaterThanTestCase
{
    use DatabaseTrait;
}
