<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\CriteriaHandler;
use Yiisoft\Data\Db\FilterHandler\QueryHandlerInterface;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Filter\Any;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\EqualsEmpty;
use Yiisoft\Data\Reader\Filter\EqualsNull;
use Yiisoft\Data\Reader\FilterInterface;
use Yiisoft\Db\Expression\ExpressionInterface;
use Yiisoft\Db\Query\QueryInterface;

final class CriteriaHandlerTest extends TestCase
{
    public function testHandlers(): void
    {
        $filters = [
            new All(),
            new \Yiisoft\Data\Db\Filter\All(),
            new Any(),
            new \Yiisoft\Data\Db\Filter\Any(),
            new Between('test', 1, 0),
            new \Yiisoft\Data\Db\Filter\Between('test', 1, 0),
            new Equals('column', 5),
            new \Yiisoft\Data\Db\Filter\Equals('column', 10),
            new EqualsEmpty('empty'),
            new \Yiisoft\Data\Db\Filter\EqualsEmpty('enpty'),
            new EqualsNull('null'),
            new \Yiisoft\Data\Db\Filter\EqualsNull('null'),
        ];

        $criteriaHandler = new CriteriaHandler();

        /** @var FilterInterface $filter */
        foreach ($filters as $filter) {
            $byFilter =  $criteriaHandler->getHandlerByOperator($filter);
            $byOperator = $criteriaHandler->getHandlerByOperator($filter::getOperator());

            self::assertInstanceOf(QueryHandlerInterface::class, $byFilter);
            self::assertTrue($byOperator === $byFilter);
        }
    }

    public function testWithHandlers(): void
    {
        $criteriaHandler = new CriteriaHandler();
        $testHandler = new class implements QueryHandlerInterface {
            public function getOperator(): string
            {
                return 'test-handler';
            }

            public function getCondition(array $criteria, $criteriaHandler): array|ExpressionInterface|null
            {
                return $criteria;
            }

            public function applyFilter(QueryInterface $query, FilterInterface $filter, $criteriaHandler): QueryInterface
            {
                return $query;
            }

            public function applyHaving(QueryInterface $query, FilterInterface $filter, $criteriaHandler): QueryInterface
            {
                return $query;
            }
        };

        $newCriteriaHandler = $criteriaHandler->withFilterHandlers($testHandler);

        self::assertFalse($criteriaHandler === $newCriteriaHandler);
        self::assertInstanceOf(QueryHandlerInterface::class, $newCriteriaHandler->getHandlerByOperator('test-handler'));
        self::assertInstanceOf(QueryHandlerInterface::class, $newCriteriaHandler->getHandlerByOperator('='));

        $this->expectException(LogicException::class);

        $criteriaHandler->getHandlerByOperator('test-handler');
    }

    public function testHasHandler(): void
    {
        $criteriaHandler = new CriteriaHandler();

        self::assertTrue($criteriaHandler->hasHandler('='));
        self::assertTrue($criteriaHandler->hasHandler(new Equals('column', 1)));
        self::assertFalse($criteriaHandler->hasHandler('foo'));
    }
}
