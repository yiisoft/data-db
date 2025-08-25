<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db;

use LogicException;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AndXHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsExpressionHandler;
use Yiisoft\Data\Db\FilterHandler\NoneHandler;
use Yiisoft\Data\Db\FilterHandler\OrXHandler;
use Yiisoft\Data\Db\FilterHandler\BetweenHandler;
use Yiisoft\Data\Db\FilterHandler\Context;
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
use Yiisoft\Data\Db\FilterHandler\QueryFilterHandlerInterface;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Query\QueryPartsInterface;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

use function sprintf;

/**
 * `FilterHandler` processes filters into {@see ConditionInterface} object that is used in
 * {@see QueryPartsInterface::andWhere()} and {@see QueryPartsInterface::andHaving()}.
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
     */
    public function __construct(array|null $handlers = null)
    {
        if (empty($handlers)) {
            $handlers = [
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

        $this->handlers = $this->prepareHandlers($handlers);
        $this->context = new Context($this);
    }

    public function withFilterHandlers(QueryFilterHandlerInterface ...$handlers): self
    {
        $new = clone $this;
        $new->handlers = array_merge(
            $this->handlers,
            $this->prepareHandlers($handlers),
        );
        return $new;
    }

    public function handle(FilterInterface $filter): ConditionInterface
    {
        return $this->getHandlerByOperator($filter::class)->getCondition($filter, $this->context);
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
