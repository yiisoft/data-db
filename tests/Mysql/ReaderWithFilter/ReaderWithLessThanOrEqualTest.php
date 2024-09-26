<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithLessThanOrEqualTestCase;
use Yiisoft\Data\Db\Tests\Mysql\DatabaseTrait;

final class ReaderWithLessThanOrEqualTest extends BaseReaderWithLessThanOrEqualTestCase
{
    use DatabaseTrait;
}
