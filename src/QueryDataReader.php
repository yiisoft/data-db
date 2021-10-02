<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Generator;
use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Data\Reader\Filter\FilterProcessorInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Data\Db\Processor\All;
use Yiisoft\Data\Db\Processor\Equals;
use Yiisoft\Data\Db\Processor\QueryProcessorInterface;
use Yiisoft\Db\Query\Query;

/**
 * @template TKey as array-key
 * @template TValue
 *
 * @implements DataReaderInterface<TKey, TValue>
 */
class QueryDataReader implements DataReaderInterface
{
    private Query $query;

    private ?Sort $sort = null;
    private ?FilterInterface $filter = null;

    private int $limit = 0;
    private int $offset = 0;

    private ?int $count = null;
    private ?array $data = null;

    private array $filterProcessors = [];


    public function __construct(Query $query)
    {
        $this->query = $query;
        $this->filterProcessors = $this->withFilterProcessors(
            new All(),
            new Equals()
        )->filterProcessors;
    }

    public function __clone()
    {
        $this->data = null;
    }

    /**
     * @psalm-return Generator<TValue>
     */
    public function getIterator(): Generator
    {
        $query = $this->prepareQuery();

        foreach ($query->each() as $row) {
            yield $row;
        }
    }

    public function count(): int
    {
        if ($this->count === null) {
            $query = $this->prepareQuery();
            $query->offset(null);
            $query->limit(null);
            $query->orderBy('');

            $this->count = $query->count();
        }

        return $this->count;
    }

    private function prepareQuery(): Query
    {
        $query = $this->applyFilter(clone $this->query);

        if ($this->limit) {
            $query->limit($this->limit);
        }

        if ($this->offset) {
            $query->offset($this->offset);
        }

        if ($this->sort && $order = $this->sort->getOrder())
        {
            foreach ($order as $name => $direction)
            {
                $query->addOrderBy([
                    $name => $direction === 'desc' ? SORT_DESC : SORT_ASC
                ]);
            }
        }

        return $query;
    }

    protected function applyFilter(Query $query): Query
    {
        if ($this->filter === null) {
            return $query;
        }

        $operation = $this->filter::getOperator();
        $processor = $this->filterProcessors[$operation] ?? null;

        if (!isset($this->filterProcessors[$operation])) {
            throw new RuntimeException(sprintf('Operation "%s" is not supported', $operation));
        }

        return $this->filterProcessors[$operation]->apply($query, $this->filter);
    }

    /**
     * @psalm-mutation-free
     */
    public function withOffset(int $offset): self
    {
        $new = clone $this;
        $new->offset = $offset;

        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    public function withLimit(int $limit): self
    {
        if ($limit < 0) {
            throw new InvalidArgumentException('$limit must not be less than 0.');
        }

        $new = clone $this;
        $new->limit = $limit;

        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    public function withSort(?Sort $sort): self
    {
        $new = clone $this;
        $new->sort = $sort;

        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    public function withFilter(FilterInterface $filter): self
    {
        $new = clone $this;
        $new->count = null;
        $new->filter = $filter;

        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    public function withFilterProcessors(FilterProcessorInterface ...$filterProcessors): self
    {
        $new = clone $this;

        foreach ($filterProcessors as $filterProcessor)
        {
            if ($filterProcessor instanceof QueryProcessorInterface) {
                /** @psalm-suppress ImpureMethodCall */
                $new->filterProcessors[$filterProcessor->getOperator()] = $filterProcessor;
            }
        }

        return $new;
    }

    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    public function read(): array
    {
        if ($this->data === null) {
            $this->data = $this->prepareQuery()->all();
        }

        return $this->data;
    }

    /**
     * @return mixed
     */
    public function readOne()
    {
        return $this->withLimit(1)->prepareQuery()->one();
    }
}
