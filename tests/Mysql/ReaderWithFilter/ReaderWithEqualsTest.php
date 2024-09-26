<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithEqualsTestCase;
use Yiisoft\Data\Db\Tests\Mysql\DatabaseTrait;

final class ReaderWithEqualsTest extends BaseReaderWithEqualsTestCase
{
    use DatabaseTrait;
}
