<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px" alt="Yii">
    </a>
    <h1 align="center">Yii Data DB</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/data-db/v)](https://packagist.org/packages/yiisoft/data-db)
[![Total Downloads](https://poser.pugx.org/yiisoft/data-db/downloads)](https://packagist.org/packages/yiisoft/data-db)
[![Code Coverage](https://codecov.io/gh/yiisoft/data-db/graph/badge.svg?token=9qlfGa4kI1)](https://codecov.io/gh/yiisoft/data-db)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fdata-db%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/data-db/master)
[![Static analysis](https://github.com/yiisoft/data-db/actions/workflows/static.yml/badge.svg?branch=master)](https://github.com/yiisoft/data-db/actions/workflows/static.yml?query=branch%3Amaster)
[![type-coverage](https://shepherd.dev/github/yiisoft/data-db/coverage.svg)](https://shepherd.dev/github/yiisoft/data-db)
[![psalm-level](https://shepherd.dev/github/yiisoft/data-db/level.svg)](https://shepherd.dev/github/yiisoft/data-db)

The package provides [data reader](https://github.com/yiisoft/data?tab=readme-ov-file#reading-data) and 
[data writer](https://github.com/yiisoft/data?tab=readme-ov-file#writing-data) implementations based
on [Yii DB](https://github.com/yiisoft/db) and a set of DB-specific filters.

Detailed build statuses:

| RDBMS                | Status                                                                                                                                                                                             |
|----------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| SQLite               | [![SQLite status](https://github.com/yiisoft/data-db/actions/workflows/sqlite.yml/badge.svg?branch=master)](https://github.com/yiisoft/data-db/actions/workflows/sqlite.yml?query=branch%3Amaster) |
| MySQL                | [![MySQL status](https://github.com/yiisoft/data-db/actions/workflows/mysql.yml/badge.svg?branch=master)](https://github.com/yiisoft/data-db/actions/workflows/mysql.yml?query=branch%3Amaster)                |
| PostgreSQL           | [![PostgreSQL status](https://github.com/yiisoft/data-db/actions/workflows/pgsql.yml/badge.svg?branch=master)](https://github.com/yiisoft/data-db/actions/workflows/pgsql.yml?query=branch%3Amaster)           |
| Microsoft SQL Server | [![Microsoft SQL Server status](https://github.com/yiisoft/data-db/actions/workflows/mssql.yml/badge.svg?branch=master)](https://github.com/yiisoft/data-db/actions/workflows/mssql.yml?query=branch%3Amaster) |
| Oracle               | [![Oracle status](https://github.com/yiisoft/data-db/actions/workflows/oracle.yml/badge.svg?branch=master)](https://github.com/yiisoft/data-db/actions/workflows/oracle.yml?query=branch%3Amaster)             |

## Requirements

- PHP 8.1 - 8.5.

## Installation

The package could be installed with [Composer](https://getcomposer.org):

```shell
composer require yiisoft/data-db
```

## General usage

The `QueryDataReader` wraps a database query to provide a flexible data reading interface:

```php
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\AndX;
use Yiisoft\Data\Reader\Sort;
use Yiisoft\Db\Query\Query;

$query = (new Query($db))->from('customer');
$dataReader = new QueryDataReader($query);

// Iterate through results
foreach ($dataReader->read() as $customer) {
    // ... process each customer ...
}

// Read a single record
$customer = $dataReader->readOne();

// Get total count
$total = $dataReader->count();

// Sorting
$sort = Sort::any(['name', 'email'])->withOrderString('-name,email');
$dataReader = $dataReader->withSort($sort);

// Filtering
$filter = new AndX(
    new Equals('status', 'active'),
    new GreaterThan('age', 18),
    new Like('name', 'John')
);
$dataReader = $dataReader->withFilter($filter);

// Pagination
$dataReader = $dataReader
    ->withOffset(20)
    ->withLimit(10);
```

### Field mapping

Map data reader field names to database columns:

```php
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\Filter\Equals;

$dataReader = new QueryDataReader(
    query: $query,
    fieldMapper: [
        'userName' => 'user_name',
        'createdAt' => 'created_at',
    ]
);

// Now you can filter and sort by 'userName' and it will use 'user_name' column
$filter = new Equals('userName', 'admin');
```

### Batch processing

Process large datasets in batches to reduce memory usage:

```php
use Yiisoft\Data\Db\QueryDataReader;

$dataReader = new QueryDataReader($query);
$dataReader = $dataReader->withBatchSize(100);

foreach ($dataReader->read() as $item) {
    // Items are fetched in batches of 100
}
```

### QueryDataWriter

The `QueryDataWriter` allows writing (inserting/updating) and deleting data to/from a database table:

```php
use Yiisoft\Data\Db\QueryDataWriter;

$writer = new QueryDataWriter($db, 'customer');

// Write items (insert or update)
$writer->write([
    ['id' => 1, 'name' => 'John', 'email' => 'john@example.com'],
    ['id' => 2, 'name' => 'Jane', 'email' => 'jane@example.com'],
]);

// Delete items
$writer->delete([
    ['id' => 1],
    ['id' => 2],
]);
```

By default, `QueryDataWriter` uses UPSERT operations (insert or update). You can configure this behavior:

```php
// Use plain INSERT instead of UPSERT
$writer = new QueryDataWriter(
    db: $db,
    table: 'customer',
    primaryKey: ['id'],
    useUpsert: false
);
```

For tables with composite primary keys:

```php
$writer = new QueryDataWriter(
    db: $db,
    table: 'order_items',
    primaryKey: ['order_id', 'product_id']
);

// Delete with composite key
$writer->delete([
    ['order_id' => 1, 'product_id' => 101],
    ['order_id' => 1, 'product_id' => 102],
]);
```

## Documentation

- [Internals](docs/internals.md)

If you need help or have a question, the [Yii Forum](https://forum.yiiframework.com/c/yii-3-0/63) is a good place for
that. You may also check out other [Yii Community Resources](https://www.yiiframework.com/community).

## License

The Yii Data DB is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
