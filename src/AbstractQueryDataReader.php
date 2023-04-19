<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Generator;
use InvalidArgumentException;
use RuntimeException;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AnyHandler;
use Yiisoft\Data\Db\FilterHandler\BetweenHandler;
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
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Query\QueryInterface;

use function array_shift;
use function is_array;
use function sprintf;

use const SORT_ASC;
use const SORT_DESC;

/**
 * @template TKey as array-key
 * @template TValue as array|object
 *
 * @implements DataReaderInterface<TKey, TValue>
 */
abstract class AbstractQueryDataReader implements QueryDataReaderInterface, DataReaderInterface
{
    private QueryInterface $query;
    private ?Sort $sort = null;
    private ?FilterInterface $filter = null;
    private ?FilterInterface $having = null;
    private int $limit = 0;
    private int $offset = 0;
    private ?int $count = null;
    private ?array $data = null;
    private int $batchSize = 100;

    private ?string $countParam = null;

    /**
     * @var QueryHandlerInterface[]
     * @psalm-var array<string, QueryHandlerInterface>
     */
    protected array $filterHandlers = [];

    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
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
            new IsNullHandler()
        );
    }

    public function __clone()
    {
        $this->data = null;
    }

    public function getIterator(): Generator
    {
        yield from $this->read();
    }

    /**
     * @return int
     * @throws \Throwable
     * @throws \Yiisoft\Db\Exception\Exception
     * @throws \Yiisoft\Db\Exception\InvalidConfigException
     */
    public function count(): int
    {
        if ($this->count === null) {
            $q = $this->countParam ?? '*';
            $query = $this->getPreparedQuery();
            $query->offset(null);
            $query->limit(null);
            $query->orderBy('');

            $this->count = (int) $query->count($q);
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

        if ($this->sort && $order = $this->sort->getOrder()) {
            foreach ($order as $name => $direction) {
                $query->addOrderBy([
                    $name => $direction === 'desc' ? SORT_DESC : SORT_ASC,
                ]);
            }
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
     */
    public function withOffset(int $offset): static
    {
        $new = clone $this;
        $new->offset = $offset;

        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    public function withLimit(int $limit): static
    {
        if ($limit < 0) {
            throw new InvalidArgumentException('$limit must not be less than 0.');
        }

        $new = clone $this;
        $new->limit = $limit;

        return $new;
    }

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
     */
    public function withSort(?Sort $sort): static
    {
        $new = clone $this;
        $new->sort = $sort;

        return $new;
    }

    /**
     * @psalm-mutation-free
     */
    public function withFilter(FilterInterface $filter): static
    {
        $new = clone $this;
        $new->count = null;
        $new->filter = $filter;

        return $new;
    }

    public function withHaving(?FilterInterface $having): static
    {
        $new = clone $this;
        $new->count = null;
        $new->having = $having;

        return $new;
    }

    public function withBatchSize(int $batchSize): static
    {
        if ($batchSize < 1) {
            throw new InvalidArgumentException('$batchSize must not be less than 1.');
        }

        if ($this->batchSize === $batchSize) {
            return $this;
        }

        $new = clone $this;
        $new->batchSize = $batchSize;

        return $new;
    }

    /**
     * @param FilterHandlerInterface ...$filterHandlers
     * @return $this
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function withFilterHandlers(FilterHandlerInterface ...$filterHandlers): static
    {
        $new = clone $this;
        $new->filterHandlers = array_merge(
            $this->filterHandlers,
            $this->prepareHandlers(...$filterHandlers)
        );

        return $new;
    }

    /**
     * @param QueryHandlerInterface ...$queryHandlers
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
     */
    public function readOne(): array|object|null
    {
        if (is_array($this->data)) {
            $data = $this->data;

            return array_shift($data);
        }

        return $this->withLimit(1)->getIterator()->current();
    }
}
