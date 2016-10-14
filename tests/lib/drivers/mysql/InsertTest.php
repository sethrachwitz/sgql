<?php

namespace SGQL\Lib\Drivers;

include_once(dirname(__FILE__).'/../../../../src/lib/drivers/mysql.php');
include_once(dirname(__FILE__).'/../../../MySQL_Database_TestCase.php');

class InsertTest extends \SGQL\MySQL_Database_TestCase {
    protected $fixture = [
        [
            'default1Wireframe.sql',
        ],
        true
    ];

    public function testInsert() {
        $driver = new MySQL(self::$hosts);
        $driver->useDatabase('sgql_unittests_data_1');

        $query = $driver->newQuery()
            ->insert('orders')
            ->values([
                [
                    'cost' => 55,
                    'shipped' => 0,
                ],
            ]);

        $results = $driver->query($query);

        $this->assertEquals(1, $results->startInsertId());
        $this->assertEquals(1, $results->affectedRows());
    }

    public function testInsertOptionalValuesKey() {
        $driver = new MySQL(self::$hosts);
        $driver->useDatabase('sgql_unittests_data_1');

        $query = $driver->newQuery()
            ->insert('customers')
            ->values([
                [
                    'name' => 'Bob Doyle',
                    'type' => 3,
                ],
                [
                    'name' => 'Elon Musk',
                ],
            ]);

        $results = $driver->query($query);

        $this->assertEquals(1, $results->startInsertId());
        $this->assertEquals(2, $results->affectedRows());
    }
}
