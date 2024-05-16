<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Support;

use Yiisoft\Db\Query\Query;

use function count;

final class CustomerQuery extends Query
{
    private array $data = [
        [
            'id' => 1,
            'email' => 'user1@example.com',
            'user1' => 'address1',
        ],
        [
            'id' => 2,
            'email' => 'user2@example.com',
            'user1' => 'address2',
        ],
        [
            'id' => 3,
            'email' => 'user3@example.com',
            'user1' => 'address3',
        ],
    ];

    public function all(): array
    {
        return $this->data;
    }

    public function one(): ?array
    {
        return $this->data[0];
    }

    public function count(string $q = '*'): int|string
    {
        return count($this->data);
    }

    public function asObject(bool $value): self
    {
        $this->data = array_map(
            static fn ($item): object|array => $value ? (object) $item : (array) $item,
            $this->data,
        );

        return $this;
    }
}
