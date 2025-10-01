<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use Generator;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use Yiisoft\Data\Db\FieldMapper\ArrayFieldMapper;
use Yiisoft\Data\Db\FieldMapper\FieldMapperInterface;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AndXHandler;
use Yiisoft\Data\Db\FilterHandler\BetweenHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsExpressionHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsNullHandler;
use Yiisoft\Data\Db\FilterHandler\ExistsHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\InHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\LikeHandler;
use Yiisoft\Data\Db\FilterHandler\NoneHandler;
use Yiisoft\Data\Db\FilterHandler\NotHandler;
use Yiisoft\Data\Db\FilterHandler\OrXHandler;
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Expression\CompositeExpression;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Query\QueryInterface;

use function array_key_first;
use function count;
use function is_array;
use function is_string;
use function sprintf;

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
    private FieldMapperInterface $fieldMapper;

    /**
     * @psalm-var non-negative-int|null
     */
    private ?int $count = null;

    /**
     * @var array[]|object[]|null
     * @psalm-var array<TKey, TValue>|null
     */
    private array|null $cache = null;

    /**
     * @psalm-param non-negative-int|null $limit
     * @psalm-param list<QueryFilterHandlerInterface>|null $filterHandlers
     * @psalm-param array<string, string|ExpressionInterface>|FieldMapperInterface $fieldMapper
     */
    public function __construct(
        private readonly QueryInterface $query,
        private ?Sort $sort = null,
        private int $offset = 0,
        private ?int $limit = null,
        private ?string $countParam = null,
        private FilterInterface $filter = new All(),
        private FilterInterface $having = new All(),
        private int|null $batchSize = null,
        array|null $filterHandlers = null,
        array|FieldMapperInterface $fieldMapper = [],
    ) {
        $this->fieldMapper = is_array($fieldMapper) ? new ArrayFieldMapper($fieldMapper) : $fieldMapper;

        $filterHandlers ??= $this->getDefaultFilterHandlers();
        $this->filterHandler = new FilterHandler($filterHandlers, $this->fieldMapper);
    }

    /**
     * @psalm-return Generator<TKey, TValue, mixed, void>
     */
    final public function getIterator(): Generator
    {
        if ($this->batchSize !== null) {
            foreach ($this->getPreparedQuery()->batch($this->batchSize) as $data) {
                /** @psalm-var array<TKey, TValue> $data */
                yield from $data;
            }
            return;
        }

        if (is_array($this->cache)) {
            yield from $this->cache;
            return;
        }

        /** @psalm-var array<TKey, TValue> */
        $this->cache = $this->getPreparedQuery()->all();
        yield from $this->cache;
    }

    /**
     * @psalm-return Generator<TKey, TValue, mixed, void>
     */
    final public function read(): Generator
    {
        return $this->getIterator();
    }

    /**
     * @psalm-return TValue|null
     */
    final public function readOne(): array|object|null
    {
        if (is_array($this->cache)) {
            $key = array_key_first($this->cache);
            return $key === null ? null : $this->cache[$key];
        }

        return $this->withLimit(1)->getIterator()->current();
    }

    final public function count(): int
    {
        if ($this->count === null) {
            $q = $this->countParam ?? '*';

            if ($q === '*' && is_array($this->cache) && $this->limit === null && $this->offset === 0) {
                $this->count = count($this->cache);
            } else {
                $query = $this->getPreparedQuery();
                $query->offset(null);
                $query->limit(null);
                $query->orderBy('');

                $count = $query->count($q);
                if (is_string($count)) {
                    throw new RuntimeException(
                        sprintf(
                            'Number of records is too large to fit into a PHP integer. Got %s.',
                            $count,
                        ),
                    );
                }

                $this->count = $count;
            }
        }

        return $this->count;
    }

    final public function getPreparedQuery(): QueryInterface
    {
        $query = (clone $this->query)
            ->andWhere(
                $this->filterHandler->handle($this->filter)
            )
            ->andHaving(
                $this->filterHandler->handle($this->having)
            );

        if ($this->limit !== null) {
            $query->limit($this->limit);
        }

        $query->offset($this->offset);

        if ($this->sort !== null) {
            $query->addOrderBy(
                $this->convertSortToOrderBy($this->sort)
            );
        }

        return $query;
    }

    /**
     * @return static The new instance with the specified offset.
     *
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withOffset(int $offset): static
    {
        $new = clone $this;
        $new->cache = null;
        $new->offset = $offset;
        return $new;
    }

    /**
     * @return static The new instance with the specified limit.
     *
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withLimit(?int $limit): static
    {
        if ($limit < 0) {
            throw new InvalidArgumentException('$limit must not be less than 0.');
        }

        $new = clone $this;
        $new->cache = null;
        $new->limit = $limit;
        return $new;
    }

    /**
     * @return static The new instance with the specified count parameter.
     *
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
     * @return static The new instance with the specified sort.
     *
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withSort(?Sort $sort): static
    {
        $new = clone $this;
        $new->cache = null;
        $new->sort = $sort;
        return $new;
    }

    /**
     * @return static The new instance with the specified filter.
     *
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withFilter(FilterInterface $filter): static
    {
        $new = clone $this;
        $new->filter = $filter;
        $new->count = $new->cache = null;
        return $new;
    }

    /**
     * @return static The new instance with the specified having condition.
     *
     * @psalm-mutation-free
     * @psalm-return static<TKey, TValue>
     */
    final public function withHaving(FilterInterface $having): static
    {
        $new = clone $this;
        $new->having = $having;
        $new->count = $new->cache = null;
        return $new;
    }

    /**
     * @return static The new instance with the specified batch size.
     *
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
     * @return static The new instance with the specified filter handlers added.
     *
     * @psalm-return static<TKey, TValue>
     */
    final public function withAddedFilterHandlers(FilterHandlerInterface ...$filterHandlers): static
    {
        foreach ($filterHandlers as $handler) {
            if (!$handler instanceof QueryFilterHandlerInterface) {
                throw new LogicException(
                    sprintf(
                        'Filter handler must implement "%s".',
                        QueryFilterHandlerInterface::class,
                    )
                );
            }
        }
        /** @var QueryFilterHandlerInterface[] $filterHandlers */

        $new = clone $this;
        $new->count = $new->cache = null;
        $new->filterHandler = $this->filterHandler->withAddedFilterHandlers(...$filterHandlers);
        return $new;
    }

    final public function getSort(): ?Sort
    {
        return $this->sort;
    }

    final public function getFilter(): FilterInterface
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
     * @psalm-return list<QueryFilterHandlerInterface>
     */
    private function getDefaultFilterHandlers(): array
    {
        return [
            new AllHandler(),
            new NoneHandler(),
            new AndXHandler(),
            new OrXHandler(),
            new EqualsHandler(),
            new GreaterThanHandler(),
            new GreaterThanOrEqualHandler(),
            new LessThanHandler(),
            new LessThanOrEqualHandler(),
            new LikeHandler(),
            new InHandler(),
            new ExistsHandler(),
            new NotHandler(),
            new BetweenHandler(),
            new EqualsNullHandler(),
            new EqualsExpressionHandler(),
        ];
    }

    private function convertSortToOrderBy(Sort $sort): array
    {
        $result = [];
        foreach ($sort->getCriteria() as $field => $direction) {
            $field = $this->fieldMapper->map($field);
            if (is_string($field)) {
                $result[$field] = $direction;
            } else {
                $result[] = new CompositeExpression([
                    $field,
                    $direction === SORT_ASC ? 'ASC' : 'DESC',
                ]);
            }
        }
        return $result;
    }
}
