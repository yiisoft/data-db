<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\All as FilterAll;
use Yiisoft\Data\Db\Filter\Any as FilterAny;
use Yiisoft\Data\Db\Filter\Between as FilterBetween;
use Yiisoft\Data\Db\Filter\CompareFilter;
use Yiisoft\Data\Db\Filter\Equals as FilterEquals;
use Yiisoft\Data\Db\Filter\GreaterThan as FilterGreaterThan;
use Yiisoft\Data\Db\Filter\GreaterThanOrEqual as FilterGreaterThanOrEqual;
use Yiisoft\Data\Db\Filter\GroupFilter;
use Yiisoft\Data\Db\Filter\ILike as FilterILike;
use Yiisoft\Data\Db\Filter\In as FilterIn;
use Yiisoft\Data\Db\Filter\LessThan as FilterLessThan;
use Yiisoft\Data\Db\Filter\LessThanOrEqual as FilterLessThanOrEqual;
use Yiisoft\Data\Db\Filter\Like as FilterLike;
use Yiisoft\Data\Db\Filter\Not as FilterNot;
use Yiisoft\Data\Db\Filter\NotEquals as FilterNotEquals;
use Yiisoft\Data\Reader\FilterInterface;
use function mb_strtoupper;

final class FiltersTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        CompareFilter::$mainDateTimeFormat = 'Y-m-d H:i:s P';
    }

    public function simpleDataProvider(): array
    {
        return [
            //EqualsHandler
            [
                new FilterEquals('equals', 1),
                ['=', 'equals', 1],
            ],
            [
                new FilterEquals('equals', [1, 2, 3]),
                ['in', 'equals', [1, 2, 3]],
            ],
            [
                new FilterEquals('column', new DateTime('2011-01-01T15:03:01.012345Z')),
                ['=', 'column', '2011-01-01 15:03:01 +00:00'],
            ],
            //BetweenHandler
            [
                new FilterBetween('column', [100, 300]),
                ['between', 'column', 100, 300],
            ],
            [
                new FilterBetween('column', [100, null]),
                ['>=', 'column', 100],
            ],
            [
                new FilterBetween('column', [null, 250]),
                ['<=', 'column', 250],
            ],
            [
                new FilterBetween('column', [new DateTime('2011-01-01T15:00:01'), new DateTime('2011-01-01T15:10:01')]),
                ['between', 'column', '2011-01-01 15:00:01 +00:00', '2011-01-01 15:10:01 +00:00'],
            ],
            //GreaterThanHandler
            [
                new FilterGreaterThan('column', 1000),
                ['>', 'column', 1000],
            ],
            [
                new FilterGreaterThan('column', new DateTime('2011-01-01T15:00:01')),
                ['>', 'column', '2011-01-01 15:00:01 +00:00'],
            ],
            [
                new FilterGreaterThanOrEqual('column', 3.5),
                ['>=', 'column', 3.5],
            ],
            [
                new FilterLessThan('column', 10.7),
                ['<', 'column', 10.7],
            ],
            [
                new FilterLessThanOrEqual('column', 100),
                ['<=', 'column', 100],
            ],
            [
                new FilterIn('column', [10, 20, 30]),
                ['in', 'column', [10, 20, 30]],
            ],
            //NotHandler equals
            [
                new FilterNotEquals('column', 40),
                ['!=', 'column', 40],
            ],
            //LikeHandler
            [
                new FilterLike('column', 'foo'),
                ['like', 'column', 'foo'],
            ],
            [
                (new FilterLike('column', 'foo'))->withoutStart(),
                ['like', 'column', 'foo%', null],
            ],
            [
                (new FilterLike('column', 'foo'))->withoutEnd(),
                ['like', 'column', '%foo', null],
            ],
            [
                (new FilterLike('column', 'foo'))->withoutBoth(),
                ['like', 'column', 'foo', null],
            ],
            //ILikeHandler
            [
                new FilterILike('column', 'foo'),
                ['ilike', 'column', 'foo'],
            ],
            [
                (new FilterILike('column', 'foo'))->withoutStart(),
                ['ilike', 'column', 'foo%', null],
            ],
            [
                (new FilterILike('column', 'foo'))->withoutEnd(),
                ['ilike', 'column', '%foo', null],
            ],
            [
                (new FilterILike('column', 'foo'))->withoutBoth(),
                ['ilike', 'column', 'foo', null],
            ],
        ];
    }

    public function nullDataProvider(): array
    {
        return [
            [
                new FilterEquals('column', null),
            ],
            [
                new FilterBetween('column', null),
            ],
            [
                new FilterGreaterThan('column', null),
            ],
            [
                new FilterGreaterThanOrEqual('column', null),
            ],
            [
                new FilterLessThan('column', null),
            ],
            [
                new FilterLessThanOrEqual('column', null),
            ],
            [
                new FilterIn('column', null),
            ],
            [
                new FilterNotEquals('column', null),
                ['IS NOT', 'column', null],
            ],
            [
                new FilterLike('column', null),
            ],
            [
                (new FilterLike('column', null))->withoutStart(),
            ],
            [
                (new FilterLike('column', null))->withoutEnd(),
            ],
            [
                (new FilterLike('column', null))->withoutBoth(),
            ],
            [
                new FilterILike('column', null),
            ],
            [
                (new FilterILike('column', null))->withoutStart(),
            ],
            [
                (new FilterILike('column', null))->withoutEnd(),
            ],
            [
                (new FilterILike('column', null))->withoutBoth(),
            ],
        ];
    }

    public function groupDataProvider(): array
    {
        $filters = array_column($this->simpleDataProvider(), 0);
        $nullFilters = array_column($this->nullDataProvider(), 0);
        $map = array_map(static fn (CompareFilter $filter) => $filter
            ->withDateTimeFormat('Y-m-d H:i:s P')
            ->toCriteriaArray(), $filters);
        $nullMap = array_map(static fn ($filter) => $filter->withIgnoreNull(), $nullFilters);

        return [
            //AND
            [
                new FilterAll(...$filters),
                array_merge(['and'], $map),
            ],
            [
                new FilterAll(...$nullMap),
                [],
            ],
            //OR
            [
                new FilterAny(...$filters),
                array_merge(['or'], $map),
            ],
            [
                new FilterAny(...$nullMap),
                [],
            ],
        ];
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testSimpleFilter(FilterInterface $filter, array $expected): void
    {
        $this->assertSame($expected, $filter->toCriteriaArray());
    }

    /**
     * @dataProvider nullDataProvider
     */
    public function testWithNull(FilterInterface $filter, array $expected = ['is', 'column', null]): void
    {
        $this->assertSame($expected, $filter->toCriteriaArray());
        $this->assertSame([], $filter
            ->withIgnoreNull()
            ->toCriteriaArray());
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testNotFilter(FilterInterface $filter, array $expected): void
    {
        $not = new FilterNot($filter);
        $array = $filter->toCriteriaArray();

        if ($array[0] === 'in' || $array[0] === 'between' || $array[0] === 'like' || $array[0] === 'ilike') {
            $array[0] = 'NOT ' . mb_strtoupper($array[0]);
            $expected = $array;
        } elseif ($array[0] === '>') {
            $array[0] = '<=';
            $expected = $array;
        } elseif ($array[0] === '>=') {
            $array[0] = '<';
            $expected = $array;
        } elseif ($array[0] === '<') {
            $array[0] = '>=';
            $expected = $array;
        } elseif ($array[0] === '<=') {
            $array[0] = '>';
            $expected = $array;
        } elseif ($array[0] === 'is') {
            $array[0] = 'IS NOT';
            $expected = $array;
        } elseif ($array[0] === '=') {
            $array[0] = '!=';
            $expected = $array;
        } else {
            $expected = ['not', $expected];
        }

        $this->assertSame($expected, $not->toCriteriaArray());
    }

    /**
     * @dataProvider groupDataProvider
     */
    public function testGroupFilters(GroupFilter $filter, array $expected): void
    {
        $this->assertSame($expected, $filter->toCriteriaArray());
    }
}
