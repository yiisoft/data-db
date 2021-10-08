<?php


declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Data\Db\Filter\All as FilterAll;
use Yiisoft\Data\Db\Filter\Equals as FilterEquals;
use Yiisoft\Data\Db\Filter\GreaterThan as FilterGreaterThan;
use Yiisoft\Data\Db\Filter\GreaterThanOrEqual as FilterGreaterThanOrEqual;
use Yiisoft\Data\Db\Filter\LessThan as FilterLessThan;
use Yiisoft\Data\Db\Filter\LessThanOrEqual as FilterLessThanOrEqual;
use Yiisoft\Data\Db\Filter\Like as FilterLike;
use Yiisoft\Data\Db\Processor\All as ProcessorAll;
use Yiisoft\Data\Db\Processor\Equals as ProcessorEquals;
use Yiisoft\Data\Db\Processor\GreaterThan as ProcessorGreaterThan;
use Yiisoft\Data\Db\Processor\GreaterThanOrEqual as ProcessorGreaterThanOrEqual;
use Yiisoft\Data\Db\Processor\LessThan as ProcessorLessThan;
use Yiisoft\Data\Db\Processor\LessThanOrEqual as ProcessorLessThanOrEqual;
use Yiisoft\Data\Db\Processor\Like as ProcessorLike;
use Yiisoft\Data\Db\Processor\QueryProcessorInterface;

class DataReaderFilterTest extends TestCase
{
    const COMPARE_FILTERS = [
        FilterEquals::class,
        FilterGreaterThan::class,
        FilterGreaterThanOrEqual::class,
        FilterLessThan::class,
        FilterLessThanOrEqual::class
    ];

    public function testInterface()
    {
        $filterEquals = new FilterEquals('test', null);
        $filterGreaterThan = new FilterGreaterThan('test', 1);
        $filterGreaterThanOrEqual = new FilterGreaterThanOrEqual('test', 1);
        $filterLessThan = new FilterLessThan('test', 100);
        $filterLessThanOrEqual = new FilterLessThanOrEqual('test', 0);
        $filterLike = new FilterLike('foo', 'bar');
        $filterAll = new FilterAll(
            $filterEquals,
            $filterGreaterThan,
            $filterGreaterThanOrEqual,
            $filterLessThan,
            $filterLessThanOrEqual,
            $filterLike
        );

        $processorAll = new ProcessorAll();
        $processorEquals = new ProcessorEquals();
        $processorGreaterThan = new ProcessorGreaterThan();
        $processortGreaterThanOrEqual = new ProcessorGreaterThanOrEqual();
        $processorLessThan = new ProcessorLessThan();
        $processorLessThanOrEqual = new ProcessorLessThanOrEqual();
        $processorLike = new ProcessorLike();

        //Filters
        $this->assertEquals($filterEquals instanceof FilterInterface, true);
        $this->assertEquals($filterAll instanceof FilterInterface, true);
        $this->assertEquals($filterGreaterThan instanceof FilterInterface, true);
        $this->assertEquals($filterGreaterThanOrEqual instanceof FilterInterface, true);
        $this->assertEquals($filterLessThan instanceof FilterInterface, true);
        $this->assertEquals($filterLessThanOrEqual instanceof FilterInterface, true);
        $this->assertEquals($filterLike instanceof FilterInterface, true);

        //Processors
        $this->assertEquals($processorAll instanceof QueryProcessorInterface, true);
        $this->assertEquals($processorEquals instanceof QueryProcessorInterface, true);
        $this->assertEquals($processorGreaterThan instanceof QueryProcessorInterface, true);
        $this->assertEquals($processortGreaterThanOrEqual instanceof QueryProcessorInterface, true);
        $this->assertEquals($processorLessThan instanceof QueryProcessorInterface, true);
        $this->assertEquals($processorLessThanOrEqual instanceof QueryProcessorInterface, true);
        $this->assertEquals($processorLike instanceof QueryProcessorInterface, true);
    }

    public function testWithNull()
    {
        $filters = $nullFilters = [];

        foreach (self::COMPARE_FILTERS as $filterName)
        {
            $filter = new $filterName('test', 1);
            $nullFilter = new $filterName('test', null);

            $filters[] = $filter;
            $nullFilters[] = $nullFilter;

            $this->assertEquals([$filter::getOperator(), 'test', 1], $filter->toArray());
            $this->assertEquals(['IS', 'test', null], $nullFilter->toArray());
        }

        $filterAll = new FilterAll(...$filters);
        $filterAllNull = new FilterAll(...$nullFilters);

        $this->assertCount(count($filters) + 1, $filterAll->toArray());
        $this->assertCount(count($filters) + 1, $filterAllNull->toArray());
    }

    public function testIgnoreNull()
    {
        $filters = $nullFilters = [];

        foreach (self::COMPARE_FILTERS as $filterName)
        {
            $filter = (new $filterName('test', 1))->withIgnoreNull(true);
            $nullFilter = (new $filterName('test', null))->withIgnoreNull(true);

            $filters[] = $filter;
            $nullFilters[] = $nullFilter;

            $this->assertEquals([$filter::getOperator(), 'test', 1], $filter->toArray());
            $this->assertEquals([], $nullFilter->toArray());
        }

        $filterAll = new FilterAll(...$filters);
        $filterAllNull = new FilterAll(...$nullFilters);

        $this->assertCount(count($filters) + 1, $filterAll->toArray());
        $this->assertCount(0, $filterAllNull->toArray());
    }

    public function testLikeFilter()
    {
        $like = new FilterLike('foo', 'bar');
        $endLike = (new FilterLike('foo', 'bar'))->withStart(false);
        $startLike = (new FilterLike('foo', 'bar'))->withEnd(false);
        $equalLike = (new FilterLike('foo', 'bar'))->withStart(false)->withEnd(false);

        $this->assertEquals([FilterLike::getOperator(), 'foo', 'bar'], $like->toArray());
        $this->assertEquals([FilterLike::getOperator(), 'foo', '%bar', false], $endLike->toArray());
        $this->assertEquals([FilterLike::getOperator(), 'foo', 'bar%', false], $startLike->toArray());
        $this->assertEquals([FilterLike::getOperator(), 'foo', 'bar', false], $equalLike->toArray());
    }
}
