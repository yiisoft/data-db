<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Generator;
use InvalidArgumentException;
use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Query\QueryInterface;

use function array_key_first;
use function count;
use function is_array;

/**
 * @template TKey as array-key
 * @template TValue as array|object
 *
 * @implements QueryDataReaderInterface<TKey, TValue>
 *
 * @psalm-suppress ClassMustBeFinal This class can be extended.
 */
class QueryDataReader implements QueryDataReaderInterface
{
    private FilterHandler $filterHandler;

    private ?Sort $sort = null;
    private ?FilterInterface $filter = null;
    private ?FilterInterface $having = null;

    /**
     * @psalm-var non-negative-int|null
     */
    private ?int $limit = null;
    private int $offset = 0;

    /**
     * @psalm-var non-negative-int|null
     */
    private ?int $count = null;

    /**
     * @var array[]|object[]|null
     * @psalm-var array<TKey, TValue>|null
     */
    private ?array $data = null;
    private ?int $batchSize = 100;
    private ?string $countParam = null;

    public function __construct(
        private readonly QueryInterface $query,
        ?FilterHandler $filterHandler = null,
    ) {
        $this->filterHandler = $filterHandler ?? new FilterHandler();
    }

    /**
     * @psalm-return Generator<TKey, TValue, mixed, void>
     * @psalm-suppress InvalidReturnType
     */
    final public function getIterator(): Generator
    {
        if (is_array($this->data)) {
            yield from $this->data;
        } elseif ($this->batchSize === null) {
            yield from $this->read();
        } else {
            $iterator = $this->getPreparedQuery()->each($this->batchSize);

            /** @var array|object $row */
            foreach ($iterator as $index => $row) {
                yield $index => $this->createItem($row);
            }
        }
    }

    final public function count(): int
    {
        if ($this->count === null) {
            $q = $this->countParam ?? '*';

            if ($q === '*' && is_array($this->data) && !$this->limit && !$this->offset) {
                $this->count = count($this->data);
            } else {
                $query = $this->getPreparedQuery();
                $query->offset(null);
                $query->limit(null);
                $query->orderBy('');

                /** @psalm-var non-negative-int */
                $this->count = (int) $query->count($q);
            }
        }

        return $this->count;
    }

    final public function getPreparedQuery(): QueryInterface
    {
        $query = $this->applyFilter(clone $this->query);
        $query = $this->applyHaving($query);

        if ($this->limit) {
            $query->limit($this->limit);
        }

        if ($this->offset) {
            $query->offset($this->offset);
        }

        if ($criteria = $this->sort?->getCriteria()) {
            $query->addOrderBy($criteria);
        }

        return $query;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withOffset(int $offset): static
    {
        $new = clone $this;
        $new->data = null;
        $new->offset = $offset;

        return $new;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withLimit(?int $limit): static
    {
        if ($limit < 0) {
            throw new InvalidArgumentException('$limit must not be less than 0.');
        }

        $new = clone $this;
        $new->data = null;
        $new->limit = $limit;

        return $new;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withCountParam(?string $countParam): static
    {
        if ($this->countParam === $countParam) {
            return $this;
        }

        $new = clone $this;
        $new->count = null;
        $new->countParam = $countParam;

        return $new;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withSort(?Sort $sort): static
    {
        $new = clone $this;
        $new->data = null;
        $new->sort = $sort;

        return $new;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withFilter(?FilterInterface $filter): static
    {
        $new = clone $this;
        $new->filter = $filter;
        $new->count = $new->data = null;

        return $new;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withHaving(?FilterInterface $having): static
    {
        $new = clone $this;
        $new->having = $having;
        $new->count = $new->data = null;

        return $new;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withBatchSize(?int $batchSize): static
    {
        if ($batchSize !== null && $batchSize < 1) {
            throw new InvalidArgumentException('$batchSize cannot be less than 1.');
        }

        $new = clone $this;
        $new->batchSize = $batchSize;

        return $new;
    }

    /**
     * @psalm-return static<TKey, TValue>
     */
    final public function withAddedFilterHandlers(FilterHandlerInterface ...$filterHandlers): static
    {
        $new = clone $this;
        $new->count = $new->data = null;
        $new->filterHandler = $this->filterHandler->withFilterHandlers(...$filterHandlers);
        return $new;
    }

    final public function getSort(): ?Sort
    {
        return $this->sort;
    }

    /**
     * @psalm-return array<TKey, TValue>
     */
    final public function read(): array
    {
        if ($this->data === null) {
            $this->data = [];
            /**
             * @psalm-var TKey $key
             * @psalm-var array $row
             */
            foreach ($this->getPreparedQuery()->all() as $key => $row) {
                $this->data[$key] = $this->createItem($row);
            }
        }

        return $this->data;
    }

    /**
     * @psalm-return TValue|null
     */
    final public function readOne(): array|object|null
    {
        if (is_array($this->data)) {
            $key = array_key_first($this->data);

            return $key === null ? null : $this->data[$key];
        }

        return $this->withLimit(1)->getIterator()->current();
    }

    final public function getFilter(): ?FilterInterface
    {
        return $this->filter;
    }

    final public function getLimit(): ?int
    {
        return $this->limit;
    }

    final public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @psalm-return TValue
     */
    protected function createItem(array|object $row): array|object
    {
        /** @psalm-var TValue */
        return $row;
    }

    private function applyFilter(QueryInterface $query): QueryInterface
    {
        if ($this->filter !== null) {
            $criteria = $this->filterHandler->handle($this->filter);
            if ($criteria !== null) {
                $query = $query->andWhere($criteria->condition, $criteria->params);
            }
        }

        return $query;
    }

    private function applyHaving(QueryInterface $query): QueryInterface
    {
        if ($this->having !== null) {
            $criteria = $this->filterHandler->handle($this->having);
            if ($criteria !== null) {
                $query = $query->andHaving($criteria->condition, $criteria->params);
            }
        }

        return $query;
    }
}
