<?php


declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Reader\Filter\FilterInterface;
use Yiisoft\Data\Db\Filter\All as FilterAll;
use Yiisoft\Data\Db\Filter\Any as FilterAny;
use Yiisoft\Data\Db\Filter\Equals as FilterEquals;
use Yiisoft\Data\Db\Filter\GreaterThan as FilterGreaterThan;
use Yiisoft\Data\Db\Filter\GreaterThanOrEqual as FilterGreaterThanOrEqual;
use Yiisoft\Data\Db\Filter\LessThan as FilterLessThan;
use Yiisoft\Data\Db\Filter\LessThanOrEqual as FilterLessThanOrEqual;
use Yiisoft\Data\Db\Filter\Like as FilterLike;
use Yiisoft\Data\Db\Filter\ILike as FilterILike;
use Yiisoft\Data\Db\Filter\In as FilterIn;
use Yiisoft\Data\Db\Filter\Not as FilterNot;
use Yiisoft\Data\Db\Processor\All as ProcessorAll;
use Yiisoft\Data\Db\Processor\Any as ProcessorAny;
use Yiisoft\Data\Db\Processor\Equals as ProcessorEquals;
use Yiisoft\Data\Db\Processor\GreaterThan as ProcessorGreaterThan;
use Yiisoft\Data\Db\Processor\GreaterThanOrEqual as ProcessorGreaterThanOrEqual;
use Yiisoft\Data\Db\Processor\LessThan as ProcessorLessThan;
use Yiisoft\Data\Db\Processor\LessThanOrEqual as ProcessorLessThanOrEqual;
use Yiisoft\Data\Db\Processor\Like as ProcessorLike;
use Yiisoft\Data\Db\Processor\In as ProcessorIn;
use Yiisoft\Data\Db\Processor\QueryProcessorInterface;

class DataReaderFilterTest extends TestCase
{
    private const COMPARE_FILTERS = [
        FilterEquals::class,
        FilterGreaterThan::class,
        FilterGreaterThanOrEqual::class,
        FilterLessThan::class,
        FilterLessThanOrEqual::class,
    ];

    public function testInterface()
    {
        $filterEquals = new FilterEquals('test', null);
        $filterGreaterThan = new FilterGreaterThan('test', 1);
        $filterGreaterThanOrEqual = new FilterGreaterThanOrEqual('test', 1);
        $filterLessThan = new FilterLessThan('test', 100);
        $filterLessThanOrEqual = new FilterLessThanOrEqual('test', 0);
        $filterLike = new FilterLike('foo', 'bar');
        $filterIn = new FilterIn('in_column', [1, 2, 3]);
        $filterAll = new FilterAll(
            $filterEquals,
            $filterGreaterThan,
            $filterGreaterThanOrEqual,
            $filterLessThan,
            $filterLessThanOrEqual,
            $filterLike,
            $filterIn
        );
        $filterAny = new FilterAny(
            $filterEquals,
            $filterGreaterThan,
            $filterGreaterThanOrEqual,
            $filterLessThan,
            $filterLessThanOrEqual,
            $filterLike,
            $filterIn
        );

        $processorAll = new ProcessorAll();
        $processorAny = new ProcessorAny();
        $processorEquals = new ProcessorEquals();
        $processorGreaterThan = new ProcessorGreaterThan();
        $processortGreaterThanOrEqual = new ProcessorGreaterThanOrEqual();
        $processorLessThan = new ProcessorLessThan();
        $processorLessThanOrEqual = new ProcessorLessThanOrEqual();
        $processorLike = new ProcessorLike();
        $processorIn = new ProcessorIn();

        //Filters
        $this->assertTrue($filterEquals instanceof FilterInterface);
        $this->assertTrue($filterAll instanceof FilterInterface);
        $this->assertTrue($filterAny instanceof FilterInterface);
        $this->assertTrue($filterGreaterThan instanceof FilterInterface);
        $this->assertTrue($filterGreaterThanOrEqual instanceof FilterInterface);
        $this->assertTrue($filterLessThan instanceof FilterInterface);
        $this->assertTrue($filterLessThanOrEqual instanceof FilterInterface);
        $this->assertTrue($filterLike instanceof FilterInterface);
        $this->assertTrue($filterIn instanceof FilterInterface);

        //Processors
        $this->assertTrue($processorAll instanceof QueryProcessorInterface);
        $this->assertTrue($processorAny instanceof QueryProcessorInterface);
        $this->assertTrue($processorEquals instanceof QueryProcessorInterface);
        $this->assertTrue($processorGreaterThan instanceof QueryProcessorInterface);
        $this->assertTrue($processortGreaterThanOrEqual instanceof QueryProcessorInterface);
        $this->assertTrue($processorLessThan instanceof QueryProcessorInterface);
        $this->assertTrue($processorLessThanOrEqual instanceof QueryProcessorInterface);
        $this->assertTrue($processorLike instanceof QueryProcessorInterface);
        $this->assertTrue($processorIn instanceof QueryProcessorInterface);
    }

    public function testWithNull()
    {
        $filters = $nullFilters = [];

        foreach (self::COMPARE_FILTERS as $filterName) {
            $filter = new $filterName('test', 1);
            $nullFilter = new $filterName('test', null);

            $filters[] = $filter;
            $nullFilters[] = $nullFilter;

            $this->assertSame([$filter::getOperator(), 'test', 1], $filter->toArray());
            $this->assertSame(['IS', 'test', null], $nullFilter->toArray());
        }

        $filterAll = new FilterAll(...$filters);
        $filterAllNull = new FilterAll(...$nullFilters);

        $this->assertCount(count($filters) + 1, $filterAll->toArray());
        $this->assertCount(count($filters) + 1, $filterAllNull->toArray());
    }

    public function testIgnoreNull()
    {
        $filters = $nullFilters = [];

        foreach (self::COMPARE_FILTERS as $filterName) {
            $filter = (new $filterName('test', 1))->withIgnoreNull(true);
            $nullFilter = (new $filterName('test', null))->withIgnoreNull(true);

            $filters[] = $filter;
            $nullFilters[] = $nullFilter;

            $this->assertSame([$filter::getOperator(), 'test', 1], $filter->toArray());
            $this->assertSame([], $nullFilter->toArray());
        }

        $filterAll = new FilterAll(...$filters);
        $filterAllNull = new FilterAll(...$nullFilters);

        $this->assertCount(count($filters) + 1, $filterAll->toArray());
        $this->assertCount(0, $filterAllNull->toArray());
    }

    public function testLikeFilter()
    {
        $like = new FilterLike('foo', 'bar');
        $endLike = (new FilterLike('foo', 'bar'))->withoutStart();
        $startLike = (new FilterLike('foo', 'bar'))->withoutEnd();
        $equalLike = (new FilterLike('foo', 'bar'))->withoutEnd()->withoutStart();

        $this->assertSame([FilterLike::getOperator(), 'foo', 'bar'], $like->toArray());
        $this->assertSame([FilterLike::getOperator(), 'foo', 'bar%', false], $endLike->toArray());
        $this->assertSame([FilterLike::getOperator(), 'foo', '%bar', false], $startLike->toArray());
        $this->assertSame([FilterLike::getOperator(), 'foo', 'bar', false], $equalLike->toArray());

        $like = new FilterILike('foo', 'bar');
        $endLike = (new FilterILike('foo', 'bar'))->withoutStart();
        $startLike = (new FilterILike('foo', 'bar'))->withoutEnd();
        $equalLike = (new FilterILike('foo', 'bar'))->withoutEnd()->withoutStart();

        $this->assertSame([FilterILike::getOperator(), 'foo', 'bar'], $like->toArray());
        $this->assertSame([FilterILike::getOperator(), 'foo', 'bar%', false], $endLike->toArray());
        $this->assertSame([FilterILike::getOperator(), 'foo', '%bar', false], $startLike->toArray());
        $this->assertSame([FilterILike::getOperator(), 'foo', 'bar', false], $equalLike->toArray());
    }

    public function testInFilter()
    {
        $filterIn = new FilterIn('column', [1, 2, 3]);
        $equalsIn = new FilterEquals('column', [1, 2, 3]);
        $filterInNull = new FilterIn('column', null);
        $filterInIgnoreNull = (new FilterIn('column', null))->withIgnoreNull(true);

        $this->assertSame([FilterIn::getOperator(), 'column', [1, 2, 3]], $filterIn->toArray());
        $this->assertSame(['IS', 'column', null], $filterInNull->toArray());
        $this->assertSame([], $filterInIgnoreNull->toArray());
        $this->assertSame($filterIn->toArray(), $equalsIn->toArray());
    }

    public function testNotFilter()
    {
        $notNull = new FilterNot(new FilterEquals('column', null));

        $this->assertSame(['IS not', 'column', null], $notNull->toArray());
    }
}
