<?php

namespace SGQL\Lib\Drivers;

include_once(dirname(__FILE__).'/../../../../src/lib/drivers/mysql.php');
include_once(dirname(__FILE__).'/../../../MySQL_Database_TestCase.php');

class TransactionTest extends \SGQL\MySQL_Database_TestCase {
    protected $fixture = [
        [
            'default1Wireframe.sql',
        ],
        true
    ];

    public function testTransaction() {
        // Used to do the insert transaction
        $driver1 = new MySQL(self::$hosts);
        $driver1->useDatabase('sgql_unittests_data_1');

        // Used to check the state of the database outside of any transactions
        $driver2 = new MySQL(self::$hosts);
        $driver2->useDatabase('sgql_unittests_data_1');

        $driver1->beginTransaction();
        $insertQuery = $driver1->newQuery()
            ->insert('orders')
            ->values([
                [
                    'cost' => 55,
                    'shipped' => 0,
                ],
            ]);

        $insertResults = $driver1->query($insertQuery);

        // Test that the insert worked inside of the transaction
        $this->assertEquals(1, $insertResults->startInsertId());
        $this->assertEquals(1, $insertResults->affectedRows());

        // Don't commit the insert

        // Ensure no orders exist outside of the transaction
        $selectQuery = $driver2->newQuery()
            ->select([
                'orders' => [
                    'id'
                ],
            ])
            ->from('orders');

        $selectResults = $driver2->fetchAll($selectQuery);
        $this->assertEquals(0, sizeof($selectResults));

        // Commit insert
        $driver1->commit();

        // Check that changes are reflected
        $selectResults = $driver2->fetchAll($selectQuery);
        $this->assertEquals(1, sizeof($selectResults));
    }

    public function testRollback() {
        // Used to do the insert transaction
        $driver1 = new MySQL(self::$hosts);
        $driver1->useDatabase('sgql_unittests_data_1');

        // Used to check the state of the database outside of any transactions
        $driver2 = new MySQL(self::$hosts);
        $driver2->useDatabase('sgql_unittests_data_1');

        $driver1->beginTransaction();
        $insertQuery = $driver1->newQuery()
            ->insert('orders')
            ->values([
                [
                    'cost' => 55,
                    'shipped' => 0,
                ],
            ]);

        $insertResults = $driver1->query($insertQuery);

        // Test that the insert worked inside of the transaction
        $this->assertEquals(1, $insertResults->startInsertId());
        $this->assertEquals(1, $insertResults->affectedRows());

        // Don't commit the insert

        // Ensure no orders exist outside of the transaction
        $selectQuery = $driver2->newQuery()
            ->select([
                'orders' => [
                    'id'
                ],
            ])
            ->from('orders');

        $selectResults = $driver2->fetchAll($selectQuery);
        $this->assertEquals(0, sizeof($selectResults));

        // Rollback insert
        $driver1->rollback();

        // Check that no changes were made in either connection
        $selectResults = $driver1->fetchAll($selectQuery);
        $this->assertEquals(0, sizeof($selectResults));

        $selectResults = $driver2->fetchAll($selectQuery);
        $this->assertEquals(0, sizeof($selectResults));
    }
}
