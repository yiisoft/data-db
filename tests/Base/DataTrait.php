<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use DateTimeImmutable;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Tests\Common\FixtureTrait;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Query\Query;

use function is_object;

trait DataTrait
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

    protected function assertFixtures(array $expectedFixtureIndexes, array $actualFixtures): void
    {
        $processedActualFixtures = [];
        foreach ($actualFixtures as $fixture) {
            if (is_object($fixture)) {
                $fixture = json_decode(json_encode($fixture), associative: true);
            }

            unset($fixture['id']);
            $fixture['number'] = (int) $fixture['number'];
            $fixture['balance'] = (float) $fixture['balance'];
            $fixture['born_at'] = $fixture['born_at'] === null
                ? null
                : new DateTimeImmutable($fixture['born_at']);

            $processedActualFixtures[$fixture['number'] - 1] = $fixture;
        }

        parent::assertFixtures($expectedFixtureIndexes, $processedActualFixtures);
    }

    protected function populateDatabase(): void
    {
        /** @var PdoConnectionInterface $db */
        $db = $this->getConnection();
        if ($db->getSchema()->getTableSchema('{{%user}}') !== null) {
            return;
        }

        $columnBuilder = $db->getColumnBuilderClass();
        $db
            ->createCommand()
            ->createTable(
                '{{%user}}',
                [
                    'id' => $columnBuilder::primaryKey(),
                    'number' => $columnBuilder::integer()->notNull(),
                    'email' => $columnBuilder::string()->notNull(),
                    'balance' => $columnBuilder::float()->defaultValue(0.0)->notNull(),
                    'born_at' => $columnBuilder::datetimeWithTimezone(),
                ],
            )
            ->execute();

        $db->transaction(function (ConnectionInterface $database): void {
            foreach ($this->getFixtures() as $fixture) {
                $database->createCommand()->insert('{{%user}}', $fixture)->execute();
            }
        });
    }

    protected function dropDatabase(): void
    {
        /** @var PdoConnectionInterface $db */
        $db = $this->getConnection();
        $db->createCommand()->dropTable('{{%user}}')->execute();
    }
}
