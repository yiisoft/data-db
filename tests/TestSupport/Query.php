<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\TestSupport;

use Closure;
use Yiisoft\Cache\Dependency\Dependency;
use Yiisoft\Db\Command\CommandInterface;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Query\BatchQueryResultInterface;
use Yiisoft\Db\Query\QueryInterface;
use Yiisoft\Db\QueryBuilder\QueryBuilderInterface;

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

    public function one(): mixed
    {
        return $this->exists() ? $this->all()[0] : null;
    }

    public function count(string $q = '*'): int|string
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

    public function andWhere(array|ExpressionInterface|string $condition, array $params = []): self
    {
        return $this;
    }

    public function orWhere(array|string|ExpressionInterface $condition, array $params = []): self
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

    public function limit(Expression|int|null $limit): self
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

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
    }

    public function average(string $q): int|float|null|string
    {
        // TODO: Implement average() method.
    }

    public function max(string $q): int|float|null|string
    {
        // TODO: Implement max() method.
    }

    public function min(string $q): int|float|null|string
    {
        // TODO: Implement min() method.
    }

    public function sum(string $q): int|float|null|string
    {
        // TODO: Implement sum() method.
    }

    public function addParams(array $params): QueryInterface
    {
        // TODO: Implement addParams() method.
    }

    public function batch(int $batchSize = 100): BatchQueryResultInterface
    {
        // TODO: Implement batch() method.
    }

    public function cache(?int $duration = 3600, ?Dependency $dependency = null): QueryInterface
    {
        // TODO: Implement cache() method.
    }

    public function column(): array
    {
        // TODO: Implement column() method.
    }

    public function createCommand(): CommandInterface
    {
        // TODO: Implement createCommand() method.
    }

    public function each(int $batchSize = 100): BatchQueryResultInterface
    {
        // TODO: Implement each() method.
    }

    public function getDistinct(): ?bool
    {
        // TODO: Implement getDistinct() method.
    }

    public function getFrom(): array|null
    {
        // TODO: Implement getFrom() method.
    }

    public function getGroupBy(): array
    {
        // TODO: Implement getGroupBy() method.
    }

    public function getHaving(): string|array|ExpressionInterface|null
    {
        // TODO: Implement getHaving() method.
    }

    public function getIndexBy(): Closure|string|null
    {
        // TODO: Implement getIndexBy() method.
    }

    public function getJoin(): array
    {
        // TODO: Implement getJoin() method.
    }

    public function getLimit(): Expression|int|null
    {
        // TODO: Implement getLimit() method.
    }

    public function getOffset(): Expression|int|null
    {
        // TODO: Implement getOffset() method.
    }

    public function getOrderBy(): array
    {
        // TODO: Implement getOrderBy() method.
    }

    public function getParams(): array
    {
        // TODO: Implement getParams() method.
    }

    public function getSelect(): array
    {
        // TODO: Implement getSelect() method.
    }

    public function getSelectOption(): ?string
    {
        // TODO: Implement getSelectOption() method.
    }

    public function getTablesUsedInFrom(): array
    {
        // TODO: Implement getTablesUsedInFrom() method.
    }

    public function getUnion(): array
    {
        // TODO: Implement getUnion() method.
    }

    public function getWhere(): array|string|ExpressionInterface|null
    {
        // TODO: Implement getWhere() method.
    }

    public function getWithQueries(): array
    {
        // TODO: Implement getWithQueries() method.
    }

    public function noCache(): QueryInterface
    {
        // TODO: Implement noCache() method.
    }

    public function params(array $params): QueryInterface
    {
        // TODO: Implement params() method.
    }

    public function populate(array $rows): array
    {
        // TODO: Implement populate() method.
    }

    public function prepare(QueryBuilderInterface $builder): QueryInterface
    {
        // TODO: Implement prepare() method.
    }

    public function scalar(): bool|int|null|string|float
    {
        // TODO: Implement scalar() method.
    }

    public function shouldEmulateExecution(): bool
    {
        // TODO: Implement shouldEmulateExecution() method.
    }

    public function addGroupBy(array|string|ExpressionInterface $columns): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement addGroupBy() method.
    }

    public function addSelect(array|string|ExpressionInterface $columns): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement addSelect() method.
    }

    public function andFilterCompare(
        string $name,
        ?string $value,
        string $defaultOperator = '='
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement andFilterCompare() method.
    }

    public function andFilterHaving(array $condition): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement andFilterHaving() method.
    }

    public function andHaving(
        array|string|ExpressionInterface $condition,
        array $params = []
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement andHaving() method.
    }

    public function distinct(?bool $value = true): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement distinct() method.
    }

    public function filterHaving(array $condition): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement filterHaving() method.
    }

    public function from(array|string|ExpressionInterface $tables): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement from() method.
    }

    public function groupBy(array|string|ExpressionInterface $columns): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement groupBy() method.
    }

    public function having(
        array|string|ExpressionInterface|null $condition,
        array $params = []
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement having() method.
    }

    public function innerJoin(
        array|string $table,
        array|string $on = '',
        array $params = []
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement innerJoin() method.
    }

    public function join(
        string $type,
        array|string $table,
        array|string $on = '',
        array $params = []
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement join() method.
    }

    public function leftJoin(
        array|string $table,
        array|string $on = '',
        array $params = []
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement leftJoin() method.
    }

    public function orFilterHaving(array $condition): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement orFilterHaving() method.
    }

    public function orHaving(
        array|string|ExpressionInterface $condition,
        array $params = []
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement orHaving() method.
    }

    public function rightJoin(
        array|string $table,
        array|string $on = '',
        array $params = []
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement rightJoin() method.
    }

    public function selectOption(?string $value): QueryInterface
    {
        // TODO: Implement selectOption() method.
    }

    public function setJoin(array $value): QueryInterface
    {
        // TODO: Implement setJoin() method.
    }

    public function setUnion(array $value): QueryInterface
    {
        // TODO: Implement setUnion() method.
    }

    public function union(QueryInterface|string $sql, bool $all = false): \Yiisoft\Db\Query\QueryPartsInterface
    {
        // TODO: Implement union() method.
    }

    public function withQuery(
        QueryInterface|string $query,
        string $alias,
        bool $recursive = false
    ): \Yiisoft\Db\Query\QueryPartsInterface {
        // TODO: Implement withQuery() method.
    }

    public function withQueries(array $value): QueryInterface
    {
        // TODO: Implement withQueries() method.
    }
}
