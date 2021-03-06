<?php

namespace SGQL;

use SGQL\Lib\Graph as Graph;
use SGQL\Lib\Drivers\MySQL;

include_once(dirname(__FILE__).'/../../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../../Graph_MySQL_Database_TestCase.php');

class SelectExportTest extends Graph_MySQL_Database_TestCase {
	protected $fixture = [
		[
			'default1Wireframe.sql',
			'default1Data.sql'
		],
		true
	];

    public function testExportSelect() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
            ->select([
                'orders' => [
                    'id',
                    'price' => 'cost',
                    'customers' => [
                        'id',
                        'name',
                    ],
                ],
            ]);

        $stringQuery = new Query("SELECT `orders`:[`id`,`cost` AS `price`,`customers`:[`id`,`name`]]", self::$graph, self::$driver);

        $expected = [
            'orders' => [
                Query::PART_COLUMNS => [
                    'id',
                    'price' => 'cost',
                ],
                'namespaces' => [
                    'customers' => [
                        Query::PART_COLUMNS => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $chainedQuery->export());
        $this->assertEquals($expected, $stringQuery->export());
    }

    public function testExportSelectWithFunctions() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
            ->select([
                'customers' => [
                    'id',
                    'name',
                    'numOrders' => 'COUNT(`orders`)',
                    'orderCostSum' => 'SUM(`orders`:`price`)',
                    'orders' => [
                        'id',
                        'price' => 'cost',
                    ],
                ],
            ]);

        $stringQuery = new Query("SELECT `customers`:[`id`,`name`, COUNT(`orders`) AS `numOrders`, SUM(`orders`:`price`) AS `orderCostSum`,`orders`:[`id`,`cost` AS `price`]]", self::$graph, self::$driver);

        $expected = [
            'customers' => [
                Query::PART_COLUMNS => [
                    'id',
                    'name',
                    'numOrders' => [
                        'function' => 'COUNT',
                        'namespace' => [
                            'orders',
                        ],
                    ],
                    'orderCostSum' => [
                        'function' => 'SUM',
                        'namespace' => [
                            'orders',
                        ],
                        'column' => 'price',
                    ],
                ],
                'namespaces' => [
                    'orders' => [
                        Query::PART_COLUMNS => [
                            'id',
                            'price' => 'cost',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $chainedQuery->export());
		$this->assertEquals($expected, $stringQuery->export());
    }

    public function testSelectFunctionInvalidNamespace() {
    	$expectedException = "Invalid namespace 'orders.tags' for function 'COUNT(`orders`.`tags`)'";

		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([
                    'customers' => [
                        'id',
                        'name',
                        'numOrders' => 'COUNT(`orders`.`tags`)',
                        'orders' => [
                            'id',
                            'price' => 'cost',
                        ],
                    ],
                ]);
            $this->fail("Expected invalid namespace exception");
        } catch (\Exception $e) {
            $this->assertEquals($expectedException, $e->getMessage());
        }

        try {
			$stringQuery = new Query("SELECT `customers`:[`id`,`name`, COUNT(`orders`.`tags`) AS `numOrders`,`orders`:[`id`,`cost` AS `price`]]", self::$graph, self::$driver);
			$this->fail("Expected invalid namespace exception");
		} catch (\Exception $e) {
			$this->assertEquals($expectedException, $e->getMessage());
		}
    }

    public function testSelectFunctionInvalidColumn() {
    	$expectedException = "Invalid column 'cost' for function 'SUM(orders:cost)'";

		try {
            $chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([
                    'customers' => [
                        'id',
                        'name',
                        'orderCostSum' => 'SUM(`orders`:`cost`)', // Invalid because 'cost' was aliased to 'price'
                        'orders' => [
                            'id',
                            'price' => 'cost',
                        ],
                    ],
                ]);
            $this->fail("Expected invalid column exception");
        } catch (\Exception $e) {
            $this->assertEquals($expectedException, $e->getMessage());
        }

        try {
			$stringQuery = new Query("SELECT `customers`:[`id`,`name`, SUM(`orders`:`cost`) AS `orderCostSum`,`orders`:[`id`,`cost` AS `price`]]", self::$graph, self::$driver);
			$this->fail("Expected invalid column exception");
		} catch (\Exception $e) {
			$this->assertEquals($expectedException, $e->getMessage());
		}
    }

    public function testSelectFunctionNestedWithValidNonReturnedColumn() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
            ->select([
                'customers' => [
                    'id',
                    'name',
                    'orders' => [
                        'id',
                        'price' => 'cost',
                        'customers' => [
                            'orderCostSum' => 'SUM(`orders`:`cost`)'
                        ]
                    ],
                ],
            ]);

        $stringQuery = new Query("SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost` AS `price`,`customers`:[SUM(`orders`:`price`) AS `orderCostSum`]]]", self::$graph, self::$driver);
    }

    public function testSelectFunctionUsingColumnNotBeingReturned() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
            ->select([
                'customers' => [
                    'id',
                    'name',
                    'orderCostSum' => 'SUM(`orders`:`cost`)',
                    'numOrders' => 'COUNT(`orders`)',
                ],
            ]);

        $stringQuery = new Query("SELECT `customers`:[`id`,`name`, SUM(`orders`:`cost`) AS `orderCostSum`, COUNT(`orders`) AS `numOrders`]", self::$graph, self::$driver);

        $expected = [
            'customers' => [
                Query::PART_COLUMNS => [
                    'id',
                    'name',
                    'numOrders' => [
                        'function' => 'COUNT',
                        'namespace' => [
                            'orders',
                        ],
                    ],
                    'orderCostSum' => [
                        'function' => 'SUM',
                        'namespace' => [
                            'orders',
                        ],
                        'column' => 'cost',
                    ],
                ],
            ],
        ];

        $this->assertEquals($chainedQuery->export(), $expected);
    }

    public function testSelectNamespaceDoesNotExist() {
    	$expectedException = "Namespace 'orders.customers.passports' does not exist";

		try {
            $chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([
                    'orders' => [
                        'id',
                        'cost',
                        'customers' => [
                            'id',
                            'name',
                            'passports' => [
                                'id',
                                'date',
                            ],
                        ],
                    ],
                ]);
            $this->fail("Expected namespace does not exist exception");
        } catch (\Exception $e) {
            $this->assertEquals($expectedException, $e->getMessage());
        }

        try {
			$stringQuery = new Query("SELECT `orders`:[`id`,`cost`,`customers`:[`id`,`name`,`passports`:[`id`,`date`]]]", self::$graph, self::$driver);
			$this->fail("Expected namespace does not exist exception");
		} catch (\Exception $e) {
			$this->assertEquals($expectedException, $e->getMessage());
		}
    }

    public function testSelectColumnNameAlreadyUsed() {
    	$expectedException = "The column name 'orders.customers:name' has already been used";

		try {
            $chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([
                    'orders' => [
                        'id',
                        'cost',
                        'customers' => [
                            'id',
                            'name',
                            'name' => 'vip',
                        ],
                    ],
                ]);

            $this->fail("Expected column name already used exception");
        } catch (\Exception $e) {
            $this->assertEquals($expectedException, $e->getMessage());
        }

        try {
			$stringQuery = new Query("SELECT `orders`:[`id`,`cost`,`customers`:[`id`,`name`,`vip` AS `name`]]", self::$graph, self::$driver);
			$this->fail("Expected column name already used exception");
		} catch (\Exception $e) {
			$this->assertEquals($expectedException, $e->getMessage());
		}
    }

    public function testSelectNoColumnsForSchema() {
    	$expectedException = "No columns specified for namespace 'orders'";
		try {
            $chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([
                    'orders' => [
                        'customers' => [
                            'id',
                            'name',
                        ],
                    ],
                ]);

            $this->fail("Expected no columns exception");
        } catch (\Exception $e) {
            $this->assertEquals($expectedException, $e->getMessage());
        }

        try {
			$stringQuery = new Query("SELECT `orders`:[`customers`:[`id`,`name`]]", self::$graph, self::$driver);
			$this->fail("Expected no columns exception");
		} catch (\Exception $e) {
			$this->assertEquals($expectedException, $e->getMessage());
		}
    }

    public function testSelectNoColumnsOrRelationshipsForSchema() {
		try {
            $chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([
                    'orders' => [
                        'id',
                        'cost',
                        'customers' => [
                            // Empty
                        ],
                    ],
                ]);

            $this->fail("Expected nothing is defined exception");
        } catch (\Exception $e) {
            $this->assertEquals("Nothing is defined for namespace 'orders.customers'", $e->getMessage());
        }

        try {
			$stringQuery = new Query("SELECT `orders`:[`id`,`cost`,`customers`:[]]", self::$graph, self::$driver);
			$this->fail("Expected invalid entity name exception");
		} catch (\Exception $e) {
			$this->assertEquals('Invalid entity name at "]]" (Index 42)', $e->getMessage());
		}
    }

    public function testSelectNoNamespaces() {
		try {
            $chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([

                ]);

            $this->fail("Expected no namespace exception");
        } catch (\Exception $e) {
            $this->assertEquals("No namespace specified", $e->getMessage());
        }

        // Not possible to build a query string with no namespaces
    }

    public function testSelectEmptyNamespace() {
		try {
            $chainedQuery = (new Query(null, self::$graph, self::$driver))
                ->select([
                    [

                    ],
                ]);

            $this->fail("Expected no namespaces exception");
        } catch (\Exception $e) {
            $this->assertEquals("One or more sets of columns is not referenced by a schema name", $e->getMessage());
        }

        // Not possible to build a query string with an empty namespace
    }

    public function testAliasingPrimaryColumn() {
    	$expectedException = "The primary column for a schema cannot be aliased";

    	try {
    		$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->select([
					'customers' => [
						'newId' => 'id',
						'name',
					],
				]);

    		$this->fail("Expected primary column alias exception");
		} catch (\Exception $e) {
    		$this->assertEquals($expectedException, $e->getMessage());
		}

		try {
			$stringQuery = new Query("SELECT `customers`:[`id` AS `newId`,`name`]", self::$graph, self::$driver);
			$this->fail("Expected primary column alias exception");
		} catch (\Exception $e) {
    		$this->assertEquals($expectedException, $e->getMessage());
		}
	}

	public function testUsingProtectedColumnName() {
    	$expectedException = "'associated_id' is a protected column name for SGQL";

		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->select([
					'customers' => [
						'id',
						'name',
						'associated_id'
					],
				]);

			$this->fail("Expected protected column name exception");
		} catch (\Exception $e) {
			$this->assertEquals($expectedException, $e->getMessage());
		}

		try {
			$stringQuery = new Query("SELECT `customers`:[`id`,`name`,`associated_id`]", self::$graph, self::$driver);
			$this->fail("Expected protected column name exception");
		} catch (\Exception $e) {
			$this->assertEquals($expectedException, $e->getMessage());
		}
	}

	public function testMultipleTopLevelSchemas() {
    	$expectedException = "Only one top level schema can be specified";

    	try {
    		$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->select([
					'customers' => [
						'name',
					],
					'orders' => [
						'cost',
					],
				]);

    		$this->fail("Expected multiple top level schemas exception");
		} catch (\Exception $e) {
    		$this->assertEquals($expectedException, $e->getMessage());
		}
	}
}
