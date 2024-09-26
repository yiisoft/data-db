<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mssql\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithInTestCase;
use Yiisoft\Data\Db\Tests\Mssql\DatabaseTrait;

final class ReaderWithInTest extends BaseReaderWithInTestCase
{
    use DatabaseTrait;
}
