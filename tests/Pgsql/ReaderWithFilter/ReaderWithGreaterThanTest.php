<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithGreaterThanTestCase;
use Yiisoft\Data\Db\Tests\Pgsql\DatabaseTrait;

final class ReaderWithGreaterThanTest extends BaseReaderWithGreaterThanTestCase
{
    use DatabaseTrait;
}
