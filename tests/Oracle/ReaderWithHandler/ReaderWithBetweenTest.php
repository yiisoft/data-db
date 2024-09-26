<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Oracle\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithBetweenTestCase;
use Yiisoft\Data\Db\Tests\Oracle\DatabaseTrait;

final class ReaderWithBetweenTest extends BaseReaderWithBetweenTestCase
{
    use DatabaseTrait;
}
