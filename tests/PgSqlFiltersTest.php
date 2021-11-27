<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Expression\ArrayExpression;
use Yiisoft\Data\Db\PgSql\Filter\TsVector as FilterTsVector;
use Yiisoft\Data\Db\PgSql\Filter\RangeContains as FilterRangeContains;
use Yiisoft\Data\Db\PgSql\Filter\ArrayContains as FilterArrayContains;

class PgSqlFiltersTest extends TestCase
{
    public function testArrayContainsFilter()
    {
        $scalar = new FilterArrayContains('array_column', 10);
        $array = new FilterArrayContains('array_column', [10, 20, 30]);
        $expression = new ArrayExpression([11, 22, 33]);
        $arrayExpression = new FilterArrayContains('array_column', $expression);

        $this->assertInstanceOf(ArrayExpression::class, $scalar->toArray()[2]);
        $this->assertInstanceOf(ArrayExpression::class, $array->toArray()[2]);
        $this->assertSame($expression, $arrayExpression->toArray()[2]);
        $this->assertEquals('&&', $scalar->toArray()[0]);
        $this->assertEquals('&&', $array->toArray()[0]);
        $this->assertEquals('&&', $arrayExpression->toArray()[0]);
    }

    public function testException()
    {
        $array = new FilterRangeContains('range_column', [100, 200]);
        $this->expectException(RuntimeException::class);
        $array->toArray();
    }

    public function testRangeContainsFilter()
    {
        $array = (new FilterRangeContains('range_column', [100, 200]))->withRangeType('int4range');
        $scalar = (new FilterRangeContains('range_column', 10))->withValueType('int4');

        $this->assertEquals('&&', $array->toArray()[0]);
        $this->assertInstanceOf(Expression::class, $array->toArray()[2]);

        $this->assertEquals('@>', $scalar->toArray()[0]);
        $this->assertInstanceOf(Expression::class, $scalar->toArray()[2]);

        $this->assertEquals('RangeContains', $array->getParamName());
        $this->assertEquals('range_param', $array->withParamName('range_param')->getParamName());
    }

    public function testTsVectorFilter()
    {
        $string = new FilterTsVector('vector_column', 'foo bar');
        $all = new FilterTsVector('vector_column', ['foo', 'bar']);
        $any = $all->any();

        $this->assertEquals('foo bar', $string->toArray()[2]->getParams()[':TsVector']);
        $this->assertEquals('foo & bar', $all->toArray()[2]->getParams()[':TsVector']);
        $this->assertEquals('foo | bar', $any->toArray()[2]->getParams()[':TsVector']);

        $this->assertEquals('foo bar', $string->withParamName('vector')->toArray()[2]->getParams()[':vector']);
        $this->assertEquals('foo & bar', $all->withParamName('vector')->toArray()[2]->getParams()[':vector']);
        $this->assertEquals('foo | bar', $any->withParamName('vector')->toArray()[2]->getParams()[':vector']);

        $this->assertEquals('foo & bar:*', $all->startsWith(true)->toArray()[2]->getParams()[':TsVector']);
        $this->assertEquals('foo:* | bar:*', $any->startsWith(true)->toArray()[2]->getParams()[':TsVector']);
    }
}
