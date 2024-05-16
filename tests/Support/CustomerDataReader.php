<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Support;

use Yiisoft\Data\Db\AbstractQueryDataReader;

final class CustomerDataReader extends AbstractQueryDataReader
{
    protected function createItem(object|array $row): array|object
    {
        return new CustomerDTO();
    }
}
