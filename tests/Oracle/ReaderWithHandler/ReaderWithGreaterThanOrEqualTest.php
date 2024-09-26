<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Oracle\ReaderWithHandler;

use Yiisoft\Data\Db\Tests\Base\Reader\ReaderWithFilter\BaseReaderWithGreaterThanOrEqualTestCase;
use Yiisoft\Data\Db\Tests\Oracle\DatabaseTrait;

final class ReaderWithGreaterThanOrEqualTest extends BaseReaderWithGreaterThanOrEqualTestCase
{
    use DatabaseTrait;
}
