<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    abstract protected function getConnection(): PdoConnectionInterface;
}
