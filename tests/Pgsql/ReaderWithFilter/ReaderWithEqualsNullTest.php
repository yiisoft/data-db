<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Pgsql\ReaderWithFilter;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithEqualsNullTestCase;
use Yiisoft\Data\Db\Tests\Pgsql\DatabaseTrait;

final class ReaderWithEqualsNullTest extends BaseReaderWithEqualsNullTestCase
{
    use DatabaseTrait;
}
