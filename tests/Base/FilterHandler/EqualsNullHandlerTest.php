<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base\FilterHandler;

use Yiisoft\Data\Db\Tests\Base\TestCase;
use Yiisoft\Data\Tests\Common\Reader\FilterHandler\EqualsNullHandlerWithReaderTestTrait;

abstract class EqualsNullHandlerTest extends TestCase
{
    use EqualsNullHandlerWithReaderTestTrait;
}
