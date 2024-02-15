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
use Yiisoft\Data\Db\FilterHandler\InHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanHandler;
use Yiisoft\Data\Db\FilterHandler\LessThanOrEqualHandler;
use Yiisoft\Data\Db\FilterHandler\LikeHandler;
use Yiisoft\Data\Db\FilterHandler\NotHandler;
use Yiisoft\Data\Db\FilterHandler\QueryHandlerInterface;
use Yiisoft\Data\Reader\FilterHandlerInterface;

final class CriteriaHandler
{
    /**
     * @psalm-var array<string, QueryHandlerInterface>
     */
    private array $handlers;

    public function __construct(QueryHandlerInterface ...$handlers)
    {
        if (empty($handlers)) {
            $handlers = [
                new AllHandler(),
                new AnyHandler(),
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
                new EqualsEmptyHandler()
            ];
        }

        $this->handlers = $this->prepareHandlers($handlers);
    }

    public function withFilterHandlers(FilterHandlerInterface ...$handlers): self
    {
        foreach ($handlers as $handler) {
            if (!$handler instanceof QueryHandlerInterface) {
                throw new LogicException(
                    sprintf(
                        'Filter handler must implement "%s".',
                        QueryHandlerInterface::class,
                    )
                );
            }
        }
        /** @var QueryHandlerInterface[] $handlers */

        $new = clone $this;
        $new->handlers = array_merge(
            $this->handlers,
            $this->prepareHandlers($handlers),
        );
        return $new;
    }

    public function handle(array $criteria): ?array
    {
        if (!isset($criteria[0])) {
            throw new LogicException('Incorrect criteria array.');
        }

        $operator = $criteria[0];
        if (!is_string($operator)) {
            throw new LogicException('Criteria operator must be a string.');
        }

        $operands = array_slice($criteria, 1);

        return $this->getHandlerByOperator($operator)->getCondition($operator, $operands, $this);
    }

    private function getHandlerByOperator(string $operator): QueryHandlerInterface
    {
        if (!isset($this->handlers[$operator])) {
            throw new LogicException(sprintf('Operator "%s" is not supported', $operator));
        }

        return $this->handlers[$operator];
    }

    /**
     * @param QueryHandlerInterface[] $handlers
     *
     * @return QueryHandlerInterface[]
     * @psalm-return array<string, QueryHandlerInterface>
     */
    private function prepareHandlers(array $handlers): array
    {
        $result = [];
        foreach ($handlers as $handler) {
            $result[$handler->getOperator()] = $handler;
        }
        return $result;
    }
}
