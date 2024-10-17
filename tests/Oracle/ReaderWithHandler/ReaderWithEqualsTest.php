<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Oracle\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithEqualsTestCase;
use Yiisoft\Data\Db\Tests\Oracle\DatabaseTrait;

final class ReaderWithEqualsTest extends BaseReaderWithEqualsTestCase
{
    use DatabaseTrait;
}
