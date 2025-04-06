<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Support;

use Yiisoft\Data\Db\QueryDataReader;

final class CustomerDataReader extends QueryDataReader
{
    protected function createItem(object|array $row): array|object
    {
        return new CustomerDTO();
    }
}
