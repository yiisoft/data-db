<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql;

use Yiisoft\Data\Reader\Filter\All;
use Yiisoft\Data\Reader\Filter\Any;
use Yiisoft\Data\Reader\Filter\Between;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\EqualsNull;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\Filter\LessThanOrEqual;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\Not;

final class QueryWithFiltersTest extends \Yiisoft\Data\Db\Tests\Base\QueryWithFiltersTest
{
    use DatabaseTrait;

    public static function simpleDataProvider(): array
    {
        $data = parent::simpleDataProvider();
        $data['like'] = [
            new Like('column', 'foo'),
            "`column` LIKE '%foo%'",
        ];

        return $data;
    }

    public static function groupFilterDataProvider(): array
    {
        return [
            [
                new All(
                    new EqualsNull('null_column'),
                    new Equals('equals', 10),
                    new Between('between', 10, 20),
                    new Any(
                        new Equals('id', 8),
                        new Like('name', 'foo')
                    )
                ),
                "(`null_column` IS NULL) AND (`equals` = 10) AND (`between` BETWEEN 10 AND 20) AND ((`id` = 8) OR (`name` LIKE '%foo%'))",
            ],
            [
                new Any(
                    new GreaterThan('greater_than', 15),
                    new LessThanOrEqual('less_than_or_equal', 10),
                    new Not(new Equals('not_equals', 'test')),
                    new All(
                        new Equals('id', 8),
                        new Like('name', 'bar')
                    )
                ),
                "(`greater_than` > 15) OR (`less_than_or_equal` <= 10) OR (`not_equals` != 'test') OR ((`id` = 8) AND (`name` LIKE '%bar%'))",
            ],
            [
                new All(
                    new GreaterThan('id', 88),
                    new Any(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) AND ((`state` = 2) OR (`name` LIKE '%eva%'))",
            ],
            [
                new Any(
                    new GreaterThan('id', 88),
                    new All(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) OR ((`state` = 2) AND (`name` LIKE '%eva%'))",
            ],
            [
                new Any(
                    new GreaterThan('id', 88),
                    new Any(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) OR ((`state` = 2) OR (`name` LIKE '%eva%'))",
            ],
            [
                new All(
                    new GreaterThan('id', 88),
                    new All(
                        new Equals('state', 2),
                        new Like('name', 'eva'),
                    )
                ),
                "(`id` > 88) AND ((`state` = 2) AND (`name` LIKE '%eva%'))",
            ],
        ];
    }
}
