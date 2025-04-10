<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithGreaterThanOrEqualTestCase;
use Yiisoft\Data\Db\Tests\Pgsql\DatabaseTrait;

final class ReaderWithGreaterThanOrEqualTest extends BaseReaderWithGreaterThanOrEqualTestCase
{
    use DatabaseTrait;
}
