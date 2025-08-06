<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mssql\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithOrXTestCase;
use Yiisoft\Data\Db\Tests\Mssql\DatabaseTrait;

final class ReaderWithOrXTest extends BaseReaderWithOrXTestCase
{
    use DatabaseTrait;
}
