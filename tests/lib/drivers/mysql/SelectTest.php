<?php

namespace SGQL\Lib\Drivers;

include_once(dirname(__FILE__).'/../../../../src/lib/drivers/mysql.php');
include_once(dirname(__FILE__).'/../../../MySQL_Database_TestCase.php');

class SelectTest extends \SGQL\MySQL_Database_TestCase {
    protected $fixture = [
        [
            'default1Wireframe.sql',
            'default1Data.sql'
        ],
        false
    ];

    public function testSelect() {
        $driver = new MySQL(self::$hosts);
        $driver->useDatabase('sgql_unittests_data_1');

        $expected = [
            [
                'id' => 2,
                'cost' => 19.1,
            ],
        ];

        $query = $driver->newQuery()
            ->select([
                'orders' => [
                    'id',
                    'cost'
                ]
            ])
            ->from('orders')
            ->where('id = 2');

        $results = $driver->fetchAll($query);

        $this->assertEquals($expected, $results);
    }

    public function testSelectJoin() {
        $driver = new MySQL(self::$hosts);
        $driver->useDatabase('sgql_unittests_data_1');

        $expected = [
            [
                'id' => 1,
                'cost' => 22.5,
                'customer_id' => 1,
                'name' => 'Steve Jobs',
            ],
        ];

        $query = $driver->newQuery()
            ->select([
                'orders' => [
                    'id',
                    'cost',
                ],
                'customers' => [
                    'customer_id' => 'id',
                    'name',
                ],
            ])
            ->from('orders')
            ->join('customers', 'orders.customer = customers.id', \SGQL\Lib\Query\MySQL::LEFT_JOIN)
            ->where('orders.id = 1');

        $results = $driver->fetchAll($query);

        $this->assertEquals($expected, $results);
    }

    public function testSelectBindNamedParams() {
        $driver = new MySQL(self::$hosts);
        $driver->useDatabase('sgql_unittests_data_1');

        $expected = [
            [
                'id' => 2,
                'cost' => 19.1,
            ],
        ];

        $query = $driver->newQuery()
            ->select([
                'orders' => [
                    'id',
                    'cost'
                ]
            ])
            ->from('orders')
            ->where('id = :id')
            ->bind([
                'id' => 2
            ]);

        $results = $driver->fetchAll($query);

        $this->assertEquals($expected, $results);
    }

    public function testSelectBindUnnamedParams() {
        /* Undesired as the query writer has no idea what order the clauses are
         * built in, so they can't order the parameters for binding, but should
         * still be tested.
         */
        $driver = new MySQL(self::$hosts);
        $driver->useDatabase('sgql_unittests_data_1');

        $expected = [
            [
                'id' => 2,
                'cost' => 19.1,
            ],
        ];

        $query = $driver->newQuery()
            ->select([
                'orders' => [
                    'id',
                    'cost'
                ]
            ])
            ->from('orders')
            ->where('id = ?')
            ->bind([2]);

        $results = $driver->fetchAll($query);

        $this->assertEquals($expected, $results);
    }
}
