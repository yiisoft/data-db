<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use LogicException;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AnyHandler;
use Yiisoft\Data\Db\FilterHandler\BetweenHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsEmptyHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsNullHandler;
use Yiisoft\Data\Db\FilterHandler\ExistsHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanHandler;
use Yiisoft\Data\Db\FilterHandler\GreaterThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\ILikeHandler;
use Yiisoft\Data\Db\FilterHandler\InHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\LikeHandler;
use Yiisoft\Data\Db\FilterHandler\NotHandler;
use Yiisoft\Data\Db\FilterHandler\OrILikeHandler;
use Yiisoft\Data\Db\FilterHandler\OrLikeHandler;
use Yiisoft\Data\Db\FilterHandler\QueryHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryPartsInterface;

use function array_merge;
use function array_unshift;

/**
 * `CriteriaHandler` processes filter criteria array from {@see FilterInterface::toCriteriaArray()} into condition array
 * that is used in {@see QueryPartsInterface::andWhere()} and {@see QueryPartsInterface::andHaving()}.
 */
final class CriteriaHandler
{
    /**
     * @psalm-var array<string, QueryHandlerInterface>
     */
    private array $handlers;

    /**
     * @param QueryHandlerInterface ...$handlers
     */
    public function __construct(
        QueryHandlerInterface ...$handlers
    ) {
        if ($handlers === []) {
            $handlers = [
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
                new NotHandler(),
                new BetweenHandler(),
                new EqualsNullHandler(),
                new EqualsEmptyHandler(),
            ];
        }

        $this->handlers = $this->prepareHandlers(...$handlers);
    }

    public function withFilterHandlers(QueryHandlerInterface $handler, QueryHandlerInterface ...$handlers): self
    {
        array_unshift($handlers, $handler);

        $new = clone $this;
        $new->handlers = array_merge(
            $this->handlers,
            $this->prepareHandlers(...$handlers),
        );
        return $new;
    }

    public function hasHandler(string|FilterInterface $operator): bool
    {
        if ($operator instanceof FilterInterface) {
            $operator = $operator::getOperator();
        }

        return isset($this->handlers[$operator]);
    }

    public function getHandlerByOperator(string|FilterInterface $operator): QueryHandlerInterface
    {
        if ($operator instanceof FilterInterface) {
            $operator = $operator::getOperator();
        }

        if (!$this->hasHandler($operator)) {
            throw new LogicException(sprintf('Operator "%s" is not supported', $operator));
        }

        return $this->handlers[$operator];
    }

    /**
     * @param QueryHandlerInterface ...$handlers
     *
     * @return QueryHandlerInterface[]
     * @psalm-return array<string, QueryHandlerInterface>
     */
    private function prepareHandlers(QueryHandlerInterface ...$handlers): array
    {
        $result = [];
        foreach ($handlers as $handler) {
            $result[$handler->getOperator()] = $handler;
        }
        return $result;
    }
}
