<?php

namespace SGQL\Lib\Config;

use SGQL\Lib\Drivers as Drivers;
use SGQL\Lib\Graph as Graph;

class Config_TestCase extends \PHPUnit_Framework_TestCase {
    protected static $driver;
    protected static $dataGraph;

    protected static $config = [
        'database' => 'mysql',
        'hosts' => [
            [
                'host' => 'localhost',
                'username' => 'sgql_test_login',
                'password' => 'sgql',
                'charset' => 'utf8',
                'dbRegex' => 'sgql_unittests_login',
                'databases' => ['sgql_unittests_login'] // Only used to set up user for tests
            ],
            [
                'host' => 'localhost',
                'username' => 'sgql_test_data',
                'password' => 'sgql',
                'charset' => 'utf8',
                'dbRegex' => 'sgql_unittests_data_[1|2]',
                'databases' => ['sgql_unittests_data_1', 'sgql_unittests_data_2'], // Only used to set up user for tests
            ],
        ],
        'graphs' => [
            'sgql_unittests_data_[1|2]' => [
                'schemas' => [
                    'customers' => [
                        'id' => [
                            'type' => 'integer',
                            'index' => 'primary'
                        ],
                        'name' => [
                            'type' => 'text',
                        ],
                        'vip' => [
                            'type' => 'boolean',
                            'default' => false,
                        ],
                    ],
                    'orders' => [
                        'id' => [
                            'type' => 'integer',
                            'index' => 'primary',
                        ],
                        'cost' => [
                            'type' => 'double',
                        ],
                        'shipped' => [
                            'type' => 'boolean',
                            'default' => false,
                        ],
                    ],
                    'products' => [
                        'id' => [
                            'type' => 'integer',
                            'index' => 'primary',
                        ],
                        'name' => [
                            'type' => 'text',
                        ],
                        'price' => [
                            'type' => 'double',
                        ],
                    ],
                ],
                'relationships' => [
                    [
                        'parent' => 'customers',
                        'child' => 'orders',
                        'type' => '<-',
                    ],
                    [
                        'parent' => 'orders',
                        'child' => 'products',
                        'type' => '<->',
                    ],
                ],
                'mode' => 'closed',
            ],
        ],
    ];

    public static function setUpBeforeClass() {
        self::$driver = new Drivers\MySQL(self::$config['hosts']);
        self::$driver->useDatabase('sgql_unittests_data_1');
        self::$dataGraph = new Graph\Graph(self::$driver);
    }
}
