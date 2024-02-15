<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use DateTime;
use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\Between;
use Yiisoft\Data\Db\Filter\CompareFilter;
use Yiisoft\Data\Db\Filter\Equals;
use Yiisoft\Data\Db\Filter\EqualsEmpty;
use Yiisoft\Data\Db\Filter\GreaterThan;
use Yiisoft\Data\Db\Filter\GreaterThanOrEqual;
use Yiisoft\Data\Db\Filter\GroupFilter;
use Yiisoft\Data\Db\Filter\ILike;
use Yiisoft\Data\Db\Filter\In;
use Yiisoft\Data\Db\Filter\LessThan;
use Yiisoft\Data\Db\Filter\LessThanOrEqual;
use Yiisoft\Data\Db\Filter\Like;
use Yiisoft\Data\Db\Filter\Not;
use Yiisoft\Data\Db\Filter\NotEquals;
use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Filter\Any;
use Yiisoft\Data\Reader\FilterInterface;

use function mb_strtoupper;
use function strtolower;

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
                new Equals('equals', 1),
                ['=', 'equals', 1],
            ],
            [
                new Equals('equals', [1, 2, 3]),
                ['in', 'equals', [1, 2, 3]],
            ],
            [
                new Equals('column', new DateTime('2011-01-01T15:03:01.012345Z')),
                ['=', 'column', '2011-01-01 15:03:01 +00:00'],
            ],
            //BetweenHandler
            [
                new Between('column', [100, 300]),
                ['between', 'column', 100, 300],
            ],
            [
                new Between('column', [100, null]),
                ['>=', 'column', 100],
            ],
            [
                new Between('column', [null, 250]),
                ['<=', 'column', 250],
            ],
            [
                new Between('column', [new DateTime('2011-01-01T15:00:01'), new DateTime('2011-01-01T15:10:01')]),
                ['between', 'column', '2011-01-01 15:00:01 +00:00', '2011-01-01 15:10:01 +00:00'],
            ],
            //GreaterThanHandler
            [
                new GreaterThan('column', 1000),
                ['>', 'column', 1000],
            ],
            [
                new GreaterThan('column', new DateTime('2011-01-01T15:00:01')),
                ['>', 'column', '2011-01-01 15:00:01 +00:00'],
            ],
            [
                new GreaterThanOrEqual('column', 3.5),
                ['>=', 'column', 3.5],
            ],
            [
                new LessThan('column', 10.7),
                ['<', 'column', 10.7],
            ],
            [
                new LessThanOrEqual('column', 100),
                ['<=', 'column', 100],
            ],
            [
                new In('column', [10, 20, 30]),
                ['in', 'column', [10, 20, 30]],
            ],
            //NotHandler equals
            [
                new NotEquals('column', 40),
                ['!=', 'column', 40],
            ],
            //LikeHandler
            [
                new Like('column', 'foo'),
                ['like', 'column', 'foo'],
            ],
            [
                (new Like('column', 'foo'))->withoutStart(),
                ['like', 'column', 'foo%', null],
            ],
            [
                (new Like('column', 'foo'))->withoutEnd(),
                ['like', 'column', '%foo', null],
            ],
            [
                (new Like('column', 'foo'))->withoutBoth(),
                ['like', 'column', 'foo', null],
            ],
            //ILikeHandler
            [
                new ILike('column', 'foo'),
                ['ilike', 'column', 'foo'],
            ],
            [
                (new ILike('column', 'foo'))->withoutStart(),
                ['ilike', 'column', 'foo%', null],
            ],
            [
                (new ILike('column', 'foo'))->withoutEnd(),
                ['ilike', 'column', '%foo', null],
            ],
            [
                (new ILike('column', 'foo'))->withoutBoth(),
                ['ilike', 'column', 'foo', null],
            ],
        ];
    }

    public function nullDataProvider(): array
    {
        return [
            [
                new Equals('column', null),
            ],
            [
                new Between('column', null),
            ],
            [
                new GreaterThan('column', null),
            ],
            [
                new GreaterThanOrEqual('column', null),
            ],
            [
                new LessThan('column', null),
            ],
            [
                new LessThanOrEqual('column', null),
            ],
            [
                new In('column', null),
            ],
            [
                new NotEquals('column', null),
                ['IS NOT', 'column', null],
            ],
            [
                new Like('column', null),
            ],
            [
                (new Like('column', null))->withoutStart(),
            ],
            [
                (new Like('column', null))->withoutEnd(),
            ],
            [
                (new Like('column', null))->withoutBoth(),
            ],
            [
                new ILike('column', null),
            ],
            [
                (new ILike('column', null))->withoutStart(),
            ],
            [
                (new ILike('column', null))->withoutEnd(),
            ],
            [
                (new ILike('column', null))->withoutBoth(),
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
                new All(...$filters),
                array_merge(['and'], $map),
            ],
            [
                new All(...$nullMap),
                [],
            ],
            //OR
            [
                new Any(...$filters),
                array_merge(['or'], $map),
            ],
            [
                new Any(...$nullMap),
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
    public function testWithNull(FilterInterface $filter, array $expected = ['IS', 'column', null]): void
    {
        $this->assertSame($expected, $filter->toCriteriaArray());
        $this->assertSame([], $filter->withIgnoreNull()->toCriteriaArray());
    }

    /**
     * @dataProvider simpleDataProvider
     */
    public function testNotFilter(FilterInterface $filter, array $expected): void
    {
        $not = new Not($filter);
        $array = $filter->toCriteriaArray();

        switch (strtolower($array[0])) {
            case 'in':
            case 'between':
            case 'like':
            case 'ilike':
                $array[0] = 'NOT ' . mb_strtoupper($array[0]);
                $expected = $array;
                break;

            case '>':
                $array[0] = '<=';
                $expected = $array;
                break;

            case '>=':
                $array[0] = '<';
                $expected = $array;
                break;

            case '<':
                $array[0] = '>=';
                $expected = $array;
                break;

            case '<=':
                $array[0] = '>';
                $expected = $array;
                break;

            case 'is':
                $array[0] = 'IS NOT';
                $expected = $array;
                break;

            case '=':
                $array[0] = '!=';
                $expected = $array;

                break;

            default:
                $expected = ['not', $expected];
        }

        $this->assertSame($expected, $not->toCriteriaArray());
    }

    /**
     * @dataProvider groupDataProvider
     */
    public function testGroupFilters(All|Any $filter, array $expected): void
    {
        $this->assertSame($expected, $filter->toCriteriaArray());
    }

    public function equalsEmptyDataProvider(): array
    {
        return [
            [
                new EqualsEmpty('column'),
                [
                    'or',
                    ['IS', 'column', null],
                    ['=', 'column', ''],
                ],
            ],
            [
                (new EqualsEmpty('column'))->withFilter(new Equals('column', 0)),
                [
                    'or',
                    ['IS', 'column', null],
                    ['=', 'column', ''],
                    ['=', 'column', 0],
                ],
            ],
            [
                (new EqualsEmpty('column'))->withFilters(new LessThanOrEqual('column', 10)),
                [
                    'or',
                    ['<=', 'column', 10],
                ],
            ],
        ];
    }

    /**
     * @dataProvider equalsEmptyDataProvider
     */
    public function testEqualsEmpty(EqualsEmpty $filter, array $expected): void
    {
        $this->assertSame($filter->toCriteriaArray(), $expected);
    }
}
