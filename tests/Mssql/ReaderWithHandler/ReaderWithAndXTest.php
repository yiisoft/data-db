<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mssql\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithAndXTestCase;
use Yiisoft\Data\Db\Tests\Mssql\DatabaseTrait;

final class ReaderWithAndXTest extends BaseReaderWithAndXTestCase
{
    use DatabaseTrait;
}
