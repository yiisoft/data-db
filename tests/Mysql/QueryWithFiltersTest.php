<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Mysql;

final class QueryWithFiltersTest extends \Yiisoft\Data\Db\Tests\Base\QueryWithFiltersTest
{
    use DatabaseTrait;

    public static function simpleDataProvider(): array
    {
        $data = parent::simpleDataProvider();
        $data['like'][1] = "`column` LIKE '%foo%'";

        return $data;
    }

    public static function groupFilterDataProvider(): array
    {
        $data = parent::groupFilterDataProvider();
        $data['all, any'] = '(`null_column` IS NULL) AND '.
            '(`equals` = 10) AND ' .
            '(`between` BETWEEN 10 AND 20) AND ' .
            "((`id` = 8) OR (`name` LIKE '%foo%'))";
        $data['any, all'] = '(`greater_than` > 15) OR ' .
            '(`less_than_or_equal` <= 10) OR ' .
            "(`not_equals` != 'test') OR " .
            "((`id` = 8) AND (`name` LIKE '%bar%'))";
        $data['all, any 2'] = "(`id` > 88) AND ((`state` = 2) OR (`name` LIKE '%eva%'))";
        $data['any, all 2'] = "(`id` > 88) OR ((`state` = 2) AND (`name` LIKE '%eva%'))";
        $data['any, any'] = "(`id` > 88) OR ((`state` = 2) OR (`name` LIKE '%eva%'))";
        $data['all, all'] = "(`id` > 88) AND ((`state` = 2) AND (`name` LIKE '%eva%'))";

        return $data;
    }
}
