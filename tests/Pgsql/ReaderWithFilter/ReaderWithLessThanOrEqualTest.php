<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithLessThanOrEqualTestCase;
use Yiisoft\Data\Db\Tests\Pgsql\DatabaseTrait;

final class ReaderWithLessThanOrEqualTest extends BaseReaderWithLessThanOrEqualTestCase
{
    use DatabaseTrait;
}
