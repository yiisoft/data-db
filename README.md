<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Data Db</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/_____/v/stable.png)](https://packagist.org/packages/yiisoft/_____)
[![Total Downloads](https://poser.pugx.org/yiisoft/_____/downloads.png)](https://packagist.org/packages/yiisoft/_____)
[![Build status](https://github.com/yiisoft/_____/workflows/build/badge.svg)](https://github.com/yiisoft/_____/actions?query=workflow%3Abuild)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/_____/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/_____/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/_____/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/_____/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2F_____%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/_____/master)
[![static analysis](https://github.com/yiisoft/_____/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/_____/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/_____/coverage.svg)](https://shepherd.dev/github/yiisoft/_____)

The package provides `Yiisoft\Db\Query\Query` bindings for generic data abstractions.

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/data-db --prefer-dist
```

## General usage

```php
use Yiisoft\Data\Db\Filter\All;
use Yiisoft\Data\Db\Filter\Equals;
use Yiisoft\Data\Db\QueryDataReader;

$typeId    = filter_input(INPUT_GET, 'type_id', FILTER_VALIDATE_INT);
$countryId = filter_input(INPUT_GET, 'country_id', FILTER_VALIDATE_INT);
$parentId  = filter_input(INPUT_GET, 'parent_id', FILTER_VALIDATE_INT);

// OR
// $typeId    = $_GET['type_id'] ?? null;
// $countryId = $_GET['country_id'] ?? null;
// $parentId  = $_GET['parent_id'] ?? null;

// OR
// $params = $request->getQueryParams();
// $typeId    = $params['type_id'] ?? null;
// $countryId = $params['country_id'] ?? null;
// $parentId  = $params['parent_id'] ?? null;

// OR same with ArrayHelper::getValue();


$query = $arFactory->createQueryTo(AR::class);

$filter = new All(
    (new Equals('type_id', $typeId)),
    (new Equals('country_id', $countryId)),
    (new Equals('parent_id', $parentId))
);

$dataReader = (new QueryDataReader($query))
            ->withFilter($filter);
```

If $typeId, $countryId and $parentId equals NULL that generate SQL like:

```shell
SELECT AR::tableName().* FROM AR::tableName() WHERE type_id IS NULL AND country_id IS NULL AND parent_id IS NULL
```

If we want ignore not existing arguments (i.e. not set in $_GET/queryParams), we can use withIgnoreNull(true) method:

```php
$typeId    = 1;
$countryId = null;
$parentId  = null;

$filter = new All(
    (new Equals('type_id', $typeId))->withIgnoreNull(true),
    (new Equals('country_id', $countryId))->withIgnoreNull(true),
    (new Equals('parent_id', $parentId))->withIgnoreNull(true)
);

$dataReader = (new QueryDataReader($query))
            ->withFilter($filter);

```

That generate SQL like:

```shell
SELECT AR::tableName().* FROM AR::tableName() WHERE type_id = 1
```

If query joins several tables with same column name, pass table name as 3-th filter arguments

```php
$equalsTableOne = (new Equals('id', 1, 'table_one'))->withIgnoreNull(true);
$equalsTableTwo = (new Equals('id', 100, 'table_two'))->withIgnoreNull(true);
```

Others filters/processors will be added at the nearest time.

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii _____ is free software. It is released under the terms of the BSD License.
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
