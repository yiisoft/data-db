<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Tests\Common\FixtureTrait;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Query\Query;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use FixtureTrait;

    protected static ?PdoConnectionInterface $connection = null;

    abstract protected function makeConnection(): PdoConnectionInterface;

    protected function getConnection(): PdoConnectionInterface
    {
        if (self::$connection === null) {
            self::$connection = $this->makeConnection();
        }

        return self::$connection;
    }

    protected function setUp(): void
    {
        $this->populateDatabase();
    }

    protected function tearDown(): void
    {
        $this->dropDatabase();
    }

    protected function getReader(): DataReaderInterface
    {
        /** @var PdoConnectionInterface $db */
        $db = $this->getConnection();

        return new QueryDataReader((new Query($db))->from('user'));
    }

    protected function populateDatabase(): void
    {
        /** @var PdoConnectionInterface $db */
        $db = $this->getConnection();
        if ($db->getSchema()->getTableSchema('{{%user}}') !== null) {
            return;
        }

        $db
            ->createCommand()
            ->createTable(
                '{{%user}}',
                [
                    'id' => 'pk',
                    'number' => 'integer NOT NULL',
                    'email' => 'string(255) NOT NULL',
                    'balance' => 'float NOT NULL DEFAULT 0.0',
                    'born_at' => 'date',
                ],
            )
            ->execute();


        $fixtures = self::$fixtures;
        foreach ($fixtures as $index => $fixture) {
            $fixtures[$index]['balance'] = (string) $fixtures[$index]['balance'];
        }

        $db
            ->createCommand()
            ->batchInsert(
                '{{%user}}',
                ['number', 'email', 'balance', 'born_at'],
                $fixtures,
            )
            ->execute();
    }

    protected function dropDatabase(): void
    {
        /** @var PdoConnectionInterface $db */
        $db = $this->getConnection();
        $db->createCommand()->dropTable('{{%user}}')->execute();
    }
}
