<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Generator;
use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AnyHandler;
use Yiisoft\Data\Db\FilterHandler\BetweenHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsEmptyHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsHandler;
use Yiisoft\Data\Db\FilterHandler\ExistsHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\ILikeHandler;
use Yiisoft\Data\Db\FilterHandler\InHandler;
use Yiisoft\Data\Db\FilterHandler\IsNullHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\LikeHandler;
use Yiisoft\Data\Db\FilterHandler\NotEqualsHandler;
use Yiisoft\Data\Db\FilterHandler\NotHandler;
use Yiisoft\Data\Db\FilterHandler\OrILikeHandler;
use Yiisoft\Data\Db\FilterHandler\OrLikeHandler;
use Yiisoft\Data\Db\FilterHandler\QueryHandlerInterface;
use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Query\QueryInterface;

use function array_key_first;
use function is_array;
use function sprintf;

/**
 * @template TKey as array-key
 * @template TValue as array|object
 *
 * @implements QueryDataReaderInterface<TKey, TValue>
 */
abstract class AbstractQueryDataReader implements QueryDataReaderInterface
{
    private ?Sort $sort = null;
    private ?FilterInterface $filter = null;
    private ?FilterInterface $having = null;
    private int $limit = 0;
    private int $offset = 0;
    private ?int $count = null;

    /**
     * @var array[]|object[]|null
     * @psalm-var array<TKey, TValue>|null
     */
    private ?array $data = null;
    private ?int $batchSize = 100;
    private ?string $countParam = null;

    /**
     * @var QueryHandlerInterface[]
     * @psalm-var array<string, QueryHandlerInterface>
     */
    protected array $filterHandlers = [];

    public function __construct(private QueryInterface $query)
    {
        $this->filterHandlers = $this->prepareHandlers(
            new AllHandler(),
            new AnyHandler(),
            new EqualsHandler(),
            new GreaterThanHandler(),
            new GreaterThanOrEqualHandler(),
            new LessThanHandler(),
            new LessThanOrEqualHandler(),
            new LikeHandler(),
            new ILikeHandler(),
            new OrLikeHandler(),
            new OrILikeHandler(),
            new InHandler(),
            new ExistsHandler(),
            new NotEqualsHandler(),
            new NotHandler(),
            new BetweenHandler(),
            new IsNullHandler(),
            new EqualsEmptyHandler()
        );
    }

    /**
     * @throws \Throwable
     * @throws \Yiisoft\Db\Exception\Exception
     * @throws \Yiisoft\Db\Exception\InvalidConfigException
     *
     * @return Generator
     *
     * @psalm-return Generator<TKey, TValue, mixed, void>
     * @psalm-suppress InvalidReturnType
     */
    public function getIterator(): Generator
    {
        if (is_array($this->data)) {
            yield from $this->data;
        } elseif ($this->batchSize === null) {
            yield from $this->read();
        } else {
            $iterator = $this->getPreparedQuery()->each($this->batchSize);

            foreach ($iterator as $index => $row) {
                yield $index => $row;
            }
        }
    }

    /**
     * @throws \Throwable
     * @throws \Yiisoft\Db\Exception\Exception
     * @throws \Yiisoft\Db\Exception\InvalidConfigException
     */
    public function count(): int
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

                $this->count = (int) $query->count($q);
            }
        }

        return $this->count;
    }

    public function getPreparedQuery(): QueryInterface
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

    protected function getHandlerByOperation(string $operation): QueryHandlerInterface
    {
        if (!isset($this->filterHandlers[$operation])) {
            throw new RuntimeException(sprintf('Operation "%s" is not supported', $operation));
        }

        return $this->filterHandlers[$operation];
    }

    protected function applyFilter(QueryInterface $query): QueryInterface
    {
        if ($this->filter !== null) {
            $query = $this->getHandlerByOperation($this->filter::getOperator())
                ->applyFilter($query, $this->filter);
        }

        return $query;
    }

    protected function applyHaving(QueryInterface $query): QueryInterface
    {
        if ($this->having !== null) {
            $query = $this->getHandlerByOperation($this->having::getOperator())
                ->applyHaving($query, $this->having);
        }

        return $query;
    }

    /**
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    public function withOffset(int $offset): static
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
    public function withLimit(int $limit): static
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
    public function withCountParam(?string $countParam): static
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
    public function withSort(?Sort $sort): static
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
    public function withFilter(FilterInterface $filter): static
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
    public function withHaving(?FilterInterface $having): static
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
    public function withBatchSize(?int $batchSize): static
    {
        if ($batchSize !== null && $batchSize < 1) {
            throw new InvalidArgumentException('$batchSize cannot be less than 1.');
        }

        $new = clone $this;
        $new->batchSize = $batchSize;

        return $new;
    }

    /**
     * @param FilterHandlerInterface ...$filterHandlers
     * @return $this
     *
     * @psalm-suppress ArgumentTypeCoercion    *
     * @psalm-return static<TKey, TValue>
     */
    public function withFilterHandlers(FilterHandlerInterface ...$filterHandlers): static
    {
        $new = clone $this;
        $new->count = $new->data = null;
        $new->filterHandlers = array_merge(
            $this->filterHandlers,
            $this->prepareHandlers(...$filterHandlers)
        );

        return $new;
    }

    /**
     * @return QueryHandlerInterface[]
     */
    private function prepareHandlers(QueryHandlerInterface ...$queryHandlers): array
    {
        $handlers = [];

        foreach ($queryHandlers as $handler) {
            $handlers[$handler->getOperator()] = $handler;
        }

        return $handlers;
    }

    public function getSort(): ?Sort
    {
        return $this->sort;
    }

    /**
     * @psalm-return array<TKey, TValue>
     *
     * @throws \Throwable
     * @throws \Yiisoft\Db\Exception\Exception
     * @throws \Yiisoft\Db\Exception\InvalidConfigException
     */
    public function read(): array
    {
        if ($this->data === null) {
            $this->data = $this->getPreparedQuery()->all();
        }

        return $this->data;
    }

    /**
     * @throws \Throwable
     * @throws \Yiisoft\Db\Exception\Exception
     * @throws \Yiisoft\Db\Exception\InvalidConfigException
     *
     * @psalm-return TValue|null
     */
    public function readOne(): array|object|null
    {
        if (is_array($this->data)) {
            $key = array_key_first($this->data);

            return $key === null ? null : $this->data[$key];
        }

        return $this->withLimit(1)->getIterator()->current();
    }
}
