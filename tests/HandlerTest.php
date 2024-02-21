<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Db\FilterHandler\AbstractHandler;
use Yiisoft\Data\Db\FilterHandler\AllHandler;
use Yiisoft\Data\Db\FilterHandler\AnyHandler;
use Yiisoft\Data\Db\FilterHandler\NotHandler;
use Yiisoft\Db\QueryBuilder\Condition\NotCondition;

final class HandlerTest extends TestCase
{
    public static function handlerConditionExceptionProvider(): array
    {
        return [
            [
                ['foo' => 'bar', 'bar' => 'foo'],
                LogicException::class,
                'Incorrect criteria for the "test-handler" operator.',
            ],
            [
                ['foo'],
                LogicException::class,
                'Incorrect criteria for the "test-handler" operator.',
            ],
            [
                [10, 'foo', 'var'],
                InvalidArgumentException::class,
                '$operator must be type of "string". "int" given.',
            ],
        ];
    }

    /**
     * @dataProvider handlerConditionExceptionProvider
     * @param array $criteria
     * @param string $exception
     * @param string $message
     * @return void
     */
    public function testHandlerConditionException(array $criteria, string $exception, string $message): void
    {
        $criteriaHandler = new CriteriaHandler();
        $handler = new class extends AbstractHandler {

            public function getOperator(): string
            {
                return 'test-handler';
            }
        };

        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        $handler->getCondition($criteria, $criteriaHandler);
    }

    public function testEmptyCriteria(): void
    {
        $criteriaHandler = new CriteriaHandler();
        $handler = new class extends AbstractHandler {

            public function getOperator(): string
            {
                return 'test-handler';
            }
        };

        $allHandler = new AllHandler();
        $anyHandler = new AnyHandler();

        self::assertTrue($handler->getCondition([], $criteriaHandler) === null);
        self::assertTrue($allHandler->getCondition([], $criteriaHandler) === null);
        self::assertTrue($anyHandler->getCondition([], $criteriaHandler) === null);
    }

    public static function notHandlerExceptionProvider(): array
    {
        return [
            [
                ['foo' => 'bar'],
                LogicException::class,
                'Incorrect criteria for the "not" operator.',
            ],
            [
                ['not'],
                LogicException::class,
                '"Not" criteria must be set.',
            ],
            [
                ['not', 'foo', 'bar'],
                LogicException::class,
                '"Not" criteria must be a non zero list. "string" given.',
            ],
            [
                ['not', ['foo' => 'bar']],
                LogicException::class,
                '"Not" criteria must be a non zero list. "array" given.',
            ],
            [
                ['not', []],
                LogicException::class,
                '"Not" criteria must be a non zero list. "array" given.',
            ],
        ];
    }

    /**
     * @dataProvider notHandlerExceptionProvider
     * @param array $criteria
     * @param string $exception
     * @param string $message
     * @return void
     */
    public function testNotHandlerExceptions(array $criteria, string $exception, string $message): void
    {
        $criteriaHandler = new CriteriaHandler();
        $handler = new NotHandler();

        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        $handler->getCondition($criteria, $criteriaHandler);
    }

    public function testNotHandlerDefault(): void
    {
        $handler = new NotHandler();
        $criteriaHandler = new CriteriaHandler();

        self::assertInstanceOf(NotCondition::class, $handler->getCondition(['NOT', ['@@', 'tsvector', 'tsquery']], $criteriaHandler));
    }
}
