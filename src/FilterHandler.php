<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use LogicException;
use Yiisoft\Data\Db\FilterHandler\AllFilterHandler;
use Yiisoft\Data\Db\FilterHandler\AnyFilterHandler;
use Yiisoft\Data\Db\FilterHandler\BetweenFilterHandler;
use Yiisoft\Data\Db\FilterHandler\Criteria;
use Yiisoft\Data\Db\FilterHandler\Context;
use Yiisoft\Data\Db\FilterHandler\EqualsFilterHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsNullFilterHandler;
use Yiisoft\Data\Db\FilterHandler\ExistsFilterHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanFilterHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanOrEqualFilterHandler;
use Yiisoft\Data\Db\FilterHandler\InFilterHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanFilterHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanOrEqualFilterHandler;
use Yiisoft\Data\Db\FilterHandler\LikeFilterHandler;
use Yiisoft\Data\Db\FilterHandler\NotFilterHandler;
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryPartsInterface;

/**
 * `FilterHandler` processes filters into {@see Criteria} object that is used in {@see QueryPartsInterface::andWhere()}
 * and {@see QueryPartsInterface::andHaving()}.
 */
final class FilterHandler
{
    private Context $context;

    /**
     * @psalm-var array<string, QueryFilterHandlerInterface>
     */
    private array $handlers;

    /**
     * @param QueryFilterHandlerInterface[]|null $handlers
     * @param ValueNormalizerInterface|null $valueNormalizer
     */
    public function __construct(
        ?array $handlers = null,
        ValueNormalizerInterface $valueNormalizer = null,
    ) {
        if (empty($handlers)) {
            $handlers = [
                new AllFilterHandler(),
                new AnyFilterHandler(),
                new EqualsFilterHandler(),
                new GreaterThanFilterHandler(),
                new GreaterThanOrEqualFilterHandler(),
                new LessThanFilterHandler(),
                new LessThanOrEqualFilterHandler(),
                new LikeFilterHandler(),
                new InFilterHandler(),
                new ExistsFilterHandler(),
                new NotFilterHandler(),
                new BetweenFilterHandler(),
                new EqualsNullFilterHandler(),
            ];
        }

        $this->handlers = $this->prepareHandlers($handlers);
        $this->context = new Context($this, $valueNormalizer ?? new ValueNormalizer());
    }

    public function withFilterHandlers(FilterHandlerInterface ...$handlers): self
    {
        foreach ($handlers as $handler) {
            if (!$handler instanceof QueryFilterHandlerInterface) {
                throw new LogicException(
                    sprintf(
                        'Filter handler must implement "%s".',
                        QueryFilterHandlerInterface::class,
                    )
                );
            }
        }
        /** @var QueryFilterHandlerInterface[] $handlers */

        $new = clone $this;
        $new->handlers = array_merge(
            $this->handlers,
            $this->prepareHandlers($handlers),
        );
        return $new;
    }

    public function handle(FilterInterface $filter): ?Criteria
    {
        return $this->getHandlerByOperator($filter::class)->getCriteria($filter, $this->context);
    }

    private function getHandlerByOperator(string $operator): QueryFilterHandlerInterface
    {
        if (!isset($this->handlers[$operator])) {
            throw new LogicException(sprintf('Operator "%s" is not supported', $operator));
        }

        return $this->handlers[$operator];
    }

    /**
     * @param QueryFilterHandlerInterface[] $handlers
     *
     * @return QueryFilterHandlerInterface[]
     * @psalm-return array<class-string, QueryFilterHandlerInterface>
     */
    private function prepareHandlers(array $handlers): array
    {
        $result = [];
        foreach ($handlers as $handler) {
            $result[$handler->getFilterClass()] = $handler;
        }
        return $result;
    }
}
