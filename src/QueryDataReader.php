<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Generator;
use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Data\Db\Processor\All;
use Yiisoft\Data\Db\Processor\Any;
use Yiisoft\Data\Db\Processor\Between;
use Yiisoft\Data\Db\Processor\Equals;
use Yiisoft\Data\Db\Processor\Exists;
use Yiisoft\Data\Db\Processor\GreaterThan;
use Yiisoft\Data\Db\Processor\GreaterThanOrEqual;
use Yiisoft\Data\Db\Processor\ILike;
use Yiisoft\Data\Db\Processor\In;
use Yiisoft\Data\Db\Processor\IsNull;
use Yiisoft\Data\Db\Processor\LessThan;
use Yiisoft\Data\Db\Processor\LessThanOrEqual;
use Yiisoft\Data\Db\Processor\Like;
use Yiisoft\Data\Db\Processor\Not;
use Yiisoft\Data\Db\Processor\NotEquals;
use Yiisoft\Data\Db\Processor\QueryProcessorInterface;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Data\Reader\Filter\FilterProcessorInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Query\Query;
use Yiisoft\Db\Query\QueryInterface;

use function sprintf;

/**
 * @template TKey as array-key
 * @template TValue
 *
 * @implements DataReaderInterface<TKey, TValue>
 */
class QueryDataReader implements DataReaderInterface
{
    private QueryInterface $query;
    private ?Sort $sort = null;
    private ?FilterInterface $filter = null;
    private int $limit = 0;
    private int $offset = 0;
    private ?int $count = null;
    private ?array $data = null;
    protected array $filterProcessors = [];

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
        $this->filterProcessors = $this->withFilterProcessors(
            new All(),
            new Any(),
            new Equals(),
            new GreaterThan(),
            new GreaterThanOrEqual(),
            new LessThan(),
            new LessThanOrEqual(),
            new Like(),
            new ILike(),
            new In(),
            new Exists(),
            new NotEquals(),
            new Not(),
            new Between(),
            new IsNull()
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
        if ($this->query instanceof Query) {
            $query = $this->getPreparedQuery();
            /** @var Query $query */
            foreach ($query->each() as $row) {
                yield $row;
            }
        } else {
            yield from $this->read();
        }
    }

    public function count(string $q = '*'): int
    {
        if ($this->count === null) {
            $query = $this->getPreparedQuery();
            $query->offset(null);
            $query->limit(null);
            $query->orderBy('');

            $count = $query->count($q);
            $this->count = is_bool($count) ? 0 : (int) $count;
        }

        return $this->count;
    }

    public function getPreparedQuery(): QueryInterface
    {
        $query = $this->applyFilter(clone $this->query);

        if ($this->limit) {
            $query->limit($this->limit);
        }

        if ($this->offset) {
            $query->offset($this->offset);
        }

        if ($this->sort && $order = $this->sort->getOrder()) {
            foreach ($order as $name => $direction) {
                $query->addOrderBy([
                    $name => $direction === 'desc' ? SORT_DESC : SORT_ASC,
                ]);
            }
        }

        return $query;
    }

    protected function applyFilter(QueryInterface $query): QueryInterface
    {
        if ($this->filter === null) {
            return $query;
        }

        $operation = $this->filter::getOperator();
        $processor = $this->filterProcessors[$operation] ?? null;

        if ($processor === null) {
            throw new RuntimeException(sprintf('Operation "%s" is not supported', $operation));
        }

        return $processor->apply($query, $this->filter);
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

        foreach ($filterProcessors as $filterProcessor) {
            if ($filterProcessor instanceof QueryProcessorInterface) {
                /** @psalm-suppress ImpureMethodCall */
                $new->filterProcessors[$filterProcessor->getOperator()] = $filterProcessor;
            } else {
                throw new InvalidArgumentException('Only ' . QueryProcessorInterface::class . ' instance allowed.');
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
            $this->data = $this
                ->getPreparedQuery()
                ->all();
        }

        return $this->data;
    }

    /**
     * @return mixed
     */
    public function readOne()
    {
        return $this
            ->withLimit(1)
            ->getPreparedQuery()
            ->one();
    }
}
