<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\TestSupport;

use Yiisoft\Db\Query\QueryInterface;

class Query implements QueryInterface
{
    private const DATA = [
        [
            'id' => 1,
            'title' => 'title 1',
        ],
        [
            'id' => 2,
            'title' => 'title 2',
        ],
        [
            'id' => 3,
            'title' => 'title 3',
        ],
        [
            'id' => 4,
            'title' => 'title 4',
        ],
    ];

    private array $select = [];
    private ?int $offset = null;
    private ?int $limit = null;
    private ?string $indexBy = null;

    public function all(): array
    {
        if ($this->offset === null && $this->limit === null) {
            $data = self::DATA;
        } else {
            $offset = $this->offset ?? 0;
            $limit = $this->limit ?? count(self::DATA);
            $data = array_slice(self::DATA, $offset, $limit);
        }

        return $this->indexBy ? array_column($data, null, $this->indexBy) : $data;
    }

    public function one()
    {
        return $this->exists() ? $this->all()[0] : null;
    }

    public function count(string $q = '*')
    {
        return count($this->all());
    }

    public function exists(): bool
    {
        return !empty($this->count());
    }

    public function indexBy($column): self
    {
        $this->indexBy = $column;

        return $this;
    }

    public function where($condition, array $params = []): self
    {
        return $this;
    }

    public function andWhere(array $condition): self
    {
        return $this;
    }

    public function orWhere(array $condition): self
    {
        return $this;
    }

    public function filterWhere(array $condition): self
    {
        return $this;
    }

    public function andFilterWhere(array $condition): self
    {
        return $this;
    }

    public function orFilterWhere(array $condition): self
    {
        return $this;
    }

    public function orderBy($columns): self
    {
        return $this;
    }

    public function addOrderBy($columns): self
    {
        return $this;
    }

    public function limit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset($offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function emulateExecution(bool $value = true): self
    {
        return $this;
    }

    public function select($columns, ?string $option = null): self
    {
        $this->select = $columns;

        return $this;
    }
}
