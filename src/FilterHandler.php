<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use LogicException;
use Yiisoft\Data\Db\FieldMapper\FieldMapperInterface;
use Yiisoft\Data\Db\FilterHandler\Context;
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryPartsInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

use function sprintf;

/**
 * @internal
 *
 * `FilterHandler` processes filters into {@see ConditionInterface} object that is used in
 * {@see QueryPartsInterface::andWhere()} and {@see QueryPartsInterface::andHaving()}.
 */
final class FilterHandler
{
    private readonly Context $context;

    /**
     * @psalm-var array<string, QueryFilterHandlerInterface>
     */
    private array $handlers;

    /**
     * @psalm-param list<QueryFilterHandlerInterface> $handlers
     */
    public function __construct(array $handlers, FieldMapperInterface $fieldMapper)
    {
        $this->handlers = $this->prepareHandlers($handlers);
        $this->context = new Context($this, $fieldMapper);
    }

    public function handle(FilterInterface $filter): ConditionInterface
    {
        return $this->getHandlerByOperator($filter::class)->getCondition($filter, $this->context);
    }

    private function getHandlerByOperator(string $operator): QueryFilterHandlerInterface
    {
        if (!isset($this->handlers[$operator])) {
            throw new LogicException(
                sprintf('Operator "%s" is not supported.', $operator),
            );
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
