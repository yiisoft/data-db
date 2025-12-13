# Usage Guide

## Reading Data

The `QueryDataReader` provides a flexible interface for reading data from database tables with support for filtering,
sorting, pagination, and batch processing.

### Basic Usage

```php
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Db\Query\Query;

$query = (new Query($db))->from('customer');
$dataReader = new QueryDataReader($query);

// Iterate through results
foreach ($dataReader->read() as $customer) {
    // Process each customer
}

// Read a single record
$customer = $dataReader->readOne();

// Get total count
$total = $dataReader->count();
```

### Filtering

```php
use Yiisoft\Data\Reader\Filter\Equals;
use Yiisoft\Data\Reader\Filter\GreaterThan;
use Yiisoft\Data\Reader\Filter\Like;
use Yiisoft\Data\Reader\Filter\AndX;

$filter = new AndX(
    new Equals('status', 'active'),
    new GreaterThan('age', 18),
    new Like('name', 'John')
);

$dataReader = $dataReader->withFilter($filter);
```

### Sorting

```php
use Yiisoft\Data\Reader\Sort;

$sort = Sort::any(['name', 'email'])->withOrderString('-name,email');
$dataReader = $dataReader->withSort($sort);
```

### Pagination

```php
$dataReader = $dataReader
    ->withOffset(20)
    ->withLimit(10);
```

### Field Mapping

Map data reader field names to database columns:

```php
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

### Batch Processing

Process large datasets in batches to reduce memory usage:

```php
$dataReader = new QueryDataReader($query);
$dataReader = $dataReader->withBatchSize(100);

foreach ($dataReader->read() as $item) {
    // Items are fetched in batches of 100
}
```

## Writing Data

The `QueryDataWriter` allows writing (inserting/updating) and deleting data to/from database tables.

### Basic Usage

```php
use Yiisoft\Data\Db\QueryDataWriter;

$writer = new QueryDataWriter($db, 'customer');

// Write items (insert or update by default)
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

### Insert vs Upsert

By default, `QueryDataWriter` uses UPSERT operations (insert or update existing records). You can configure this:

```php
// Use plain INSERT instead of UPSERT
$writer = new QueryDataWriter(
    db: $db,
    table: 'customer',
    primaryKey: ['id'],
    useUpsert: false  // Will throw exception if record already exists
);
```

### Composite Primary Keys

For tables with multiple primary key columns:

```php
$writer = new QueryDataWriter(
    db: $db,
    table: 'order_items',
    primaryKey: ['order_id', 'product_id']
);

// Delete requires all primary key columns
$writer->delete([
    ['order_id' => 1, 'product_id' => 101],
    ['order_id' => 1, 'product_id' => 102],
]);
```

### Error Handling

The writer throws `DataWriterException` on errors:

```php
use Yiisoft\Data\Writer\DataWriterException;

try {
    $writer->write([
        ['id' => 1, 'name' => 'John'],
    ]);
} catch (DataWriterException $e) {
    // Handle write error
    echo "Failed to write: " . $e->getMessage();
}
```

### Validation

Each item must be an associative array:

```php
// ✓ Valid
$writer->write([
    ['id' => 1, 'name' => 'John'],
    ['id' => 2, 'name' => 'Jane'],
]);

// ✗ Invalid - will throw DataWriterException
$writer->write(['string value']);
```

For delete operations, items must contain all primary key columns:

```php
// ✓ Valid
$writer->delete([
    ['id' => 1],
]);

// ✗ Invalid - missing primary key
$writer->delete([
    ['name' => 'John'],  // Throws: "Item must contain primary key column 'id'"
]);
```

### Batch Operations

Both `write()` and `delete()` accept iterables, allowing you to process large datasets efficiently:

```php
// Generator for memory-efficient processing
function getCustomers(): Generator {
    // Fetch from another source or generate dynamically
    for ($i = 1; $i <= 1000; $i++) {
        yield ['id' => $i, 'name' => "Customer $i"];
    }
}

$writer->write(getCustomers());
```

## Combining Reader and Writer

You can combine reader and writer for data transformation:

```php
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Db\QueryDataWriter;
use Yiisoft\Data\Reader\Filter\Equals;

// Read from source table
$sourceQuery = (new Query($db))->from('source_table');
$reader = new QueryDataReader($sourceQuery);
$reader = $reader->withFilter(new Equals('status', 'pending'));

// Write to destination table
$writer = new QueryDataWriter($db, 'destination_table');

// Transform and write
$items = [];
foreach ($reader->read() as $item) {
    $items[] = [
        'id' => $item['id'],
        'name' => strtoupper($item['name']),
        'processed_at' => time(),
    ];
}
$writer->write($items);

// Mark as processed in source
$deleteItems = [];
foreach ($reader->read() as $item) {
    $deleteItems[] = ['id' => $item['id']];
}
$sourceWriter = new QueryDataWriter($db, 'source_table');
$sourceWriter->delete($deleteItems);
```
