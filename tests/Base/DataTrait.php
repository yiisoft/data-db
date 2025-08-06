<?php

declare(strict_types=1);

namespace Yiisoft\Data\Db\Tests\Base;

use DateTime;
use Yiisoft\Data\Db\QueryDataReader;
use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Data\Tests\Common\FixtureTrait;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Driver\Pdo\PdoConnectionInterface;
use Yiisoft\Db\Expression\Expression;
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

        $db
            ->createCommand()
            ->createTable(
                '{{%user}}',
                [
                    'id' => 'pk',
                    'number' => 'integer NOT NULL',
                    'email' => 'string(255) NOT NULL',
                    'balance' => 'float DEFAULT 0.0 NOT NULL',
                    'born_at' => 'date',
                ],
            )
            ->execute();

        $db->transaction(static function (ConnectionInterface $database): void {
            foreach (self::$fixtures as $fixture) {
                if ($fixture['born_at'] !== null && $database->getDriverName() === 'oci') {
                    $fixture['born_at'] = new Expression(
                        "TO_DATE(:born_at, 'yyyy-mm-dd')",
                        [':born_at' => $fixture['born_at']],
                    );
                }

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
