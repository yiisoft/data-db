<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Data\Db\Filter\All;
use Yiisoft\Data\Db\Filter\Between;
use Yiisoft\Data\Db\Filter\Equals;
use Yiisoft\Data\Db\Filter\Like;
use Yiisoft\Data\Db\Filter\ParamsTrait;
use Yiisoft\Data\Db\Filter\QueryFilterInterface;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\Tests\Support\TestTrait;
use Yiisoft\Db\Expression\Expression;
use Yiisoft\Db\Query\Query;

final class QueryParamsTest extends TestCase
{
    use TestTrait;

    public static function paramsDataProvider(): array
    {
        return [
            'equals' => [
                (new Equals('equals', new Expression(':param')))->withParam(':param', 'test'),
                "[equals] = 'test'",
            ],
            'between' => [
                new Between('column', new Expression(':min'), new Expression(':max'), [
                    ':min' => 100,
                    ':max' => 200,
                ]),
                '[column] BETWEEN 100 AND 200',
            ],
            'between-with' => [
                (new Between('column', new Expression(':min'), new Expression(':max')))->withParams([
                    ':min' => 300,
                    ':max' => 400,
                ]),
                '[column] BETWEEN 300 AND 400',
            ],
            'all' => [
                new All(
                    (new Equals('equals', new Expression(':param')))->withParam(':param', '1'),
                    new Between('column', new Expression(':min'), new Expression(':max'), [
                        ':min' => 10,
                        ':max' => 20,
                    ]),
                    new Like('text', new Expression(':like'), [':like' => '%foo-bar']),
                ),
                "([equals] = '1') AND ([column] BETWEEN 10 AND 20) AND ([text] LIKE '%foo-bar')",
            ],
        ];
    }

    /**
     * @dataProvider paramsDataProvider
     * @param QueryFilterInterface $filter
     * @param string $condition
     * @return void
     * @throws \Yiisoft\Db\Exception\Exception
     * @throws \Yiisoft\Db\Exception\InvalidConfigException
     */
    public function testParams(QueryFilterInterface $filter, string $condition): void
    {
        $db = $this->getConnection();
        $query = (new Query($db))->from('customer');
        $dataReader = (new QueryDataReader($query))->withFilter($filter);
        $expected = 'SELECT * FROM [customer] WHERE ' . $condition;

        self::assertSame(
            $expected,
            $dataReader->getPreparedQuery()->createCommand()->getRawSql(),
        );
    }

    public function testParamsTrait(): void
    {
        $object = new class {
            use ParamsTrait;
        };

        $asArray = $object->withParams(['foo' => 'bar']);
        $asParam = $asArray->withParam('foo', 'test');

        self::assertFalse($object === $asArray);
        self::assertSame(['foo' => 'bar'], $asArray->getParams());
        self::assertEmpty($object->getParams());
        self::assertFalse($object === $asParam);
        self::assertFalse($asArray === $asParam);
        self::assertSame(['foo' => 'test'], $asParam->getParams());
    }
}
