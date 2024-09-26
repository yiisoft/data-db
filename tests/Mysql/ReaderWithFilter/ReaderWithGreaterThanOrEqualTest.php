<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithGreaterThanOrEqualTestCase;
use Yiisoft\Data\Db\Tests\Mysql\DatabaseTrait;

final class ReaderWithGreaterThanOrEqualTest extends BaseReaderWithGreaterThanOrEqualTestCase
{
    use DatabaseTrait;
}
