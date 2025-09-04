<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Sqlite;

use LogicException;
use Yiisoft\Data\Db\FieldMapper\ArrayFieldMapper;
use Yiisoft\Data\Db\FilterHandler;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\EqualsHandler;
use Yiisoft\Data\Db\FilterHandler\NoneHandler;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\In;
use Yiisoft\Data\Reader\Filter\None;
use Yiisoft\Data\Tests\TestCase;
use Yiisoft\Db\QueryBuilder\Condition\ConditionInterface;

final class FilterHandlerTest extends TestCase
{
    public function testWithAddedFilterHandlers(): void
    {
        $handler1 = new EqualsHandler();
        $handler2 = new NoneHandler();
        $handler3 = new AllHandler();
        $handler = new FilterHandler([$handler1], new ArrayFieldMapper([]));

        $handlerWithAdded = $handler->withAddedFilterHandlers($handler2, $handler3);

        $this->assertNotSame($handler, $handlerWithAdded);

        $this->assertInstanceOf(ConditionInterface::class, $handlerWithAdded->handle(new Equals('field', 'value')));
        $this->assertInstanceOf(ConditionInterface::class, $handlerWithAdded->handle(new None()));
        $this->assertInstanceOf(ConditionInterface::class, $handlerWithAdded->handle(new All()));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Operator "' . In::class . '" is not supported.');
        $handlerWithAdded->handle(new In('field', []));
    }
}
