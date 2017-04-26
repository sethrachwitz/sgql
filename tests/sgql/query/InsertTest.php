<?php

include_once(dirname(__FILE__).'/../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../SGQL_MySQL_Database_TestCase.php');

class InsertTest extends SGQL_MySQL_Database_TestCase {
	protected $fixture = [
		[
			'SGQLSetup.sql',
			'default1Wireframe.sql',
			'default1Data.sql',
			'default1SGQL.sql',
			'default1ExtendedSGQL.sql',
		],
		true
	];

	protected static $complexInsert = [
		'orders' => [
			[
				'cost' => 5.5,
				'customers' => [
					[
						'name' => 'Customer 1',
						'passports' => [
							[
								'country' => 'US',
							]
						],
					],
				],
				'products' => [
					[
						'name' => 'Product 1',
						'price' => 5.5,
					],
				],
			],
			[
				'cost' => 13.2,
				'customers' => [
					[
						'name' => 'Customer 2',
						'passports' => [
							[
								'country' => 'UK',
							]
						]
					]
				],
				'products' => [
					[
						'name' => 'Product 3',
						'price' => 3.2,
					],
					[
						'name' => 'Product 2',
						'price' => 10,
					],
				],
			],
		]
	];

	public function testFlattenValues() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->insert(self::$complexInsert);

		$exported = $query->export();
		$values = $exported[\SGQL\Query::PART_VALUES];
		reset($values);
		$topLevelSchema = key($values);

		$expected = [
			'values' => [
				[
					'columns' => [
						'cost' => 5.5,
					],
				],
				[
					'columns' => [
						'cost' => 13.2,
					],
				],
			],
			'namespaces' => [
				'customers' => [
					'values' => [
						[
							'columns' => [
								'name' => 'Customer 1',
							],
							'parent_id' => 0,
						],
						[
							'columns' => [
								'name' => 'Customer 2',
							],
							'parent_id' => 1,
						],
					],
					'namespaces' => [
						'passports' => [
							'values' => [
								[
									'columns' => [
										'country' => 'US',
									],
									'parent_id' => 0,
								],
								[
									'columns' => [
										'country' => 'UK',
									],
									'parent_id' => 1,
								],
							]
						],
					],
				],
				'products' => [
					'values' => [
						[
							'columns' => [
								'name' => 'Product 1',
								'price' => 5.5,
							],
							'parent_id' => 0,
						],
						[
							'columns' => [
								'name' => 'Product 3',
								'price' => 3.2,
							],
							'parent_id' => 1,
						],
						[
							'columns' => [
								'name' => 'Product 2',
								'price' => 10,
							],
							'parent_id' => 1,
						],
					],
				],
			],
		];

		$flattened = \SGQL\Executor\Executor::flattenValues($values[$topLevelSchema]);

		$this->assertEquals($expected, $flattened);
	}

	public function testSimpleInsert() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->insert([
				'customers' => [
					[
						'name' => 'New customer',
					],
				]
			]);

		$expected = [
			'values' => [
				'customers' => [
					[
						'id' => '5',
						'name' => 'New customer',
					],
				]
			]
		];

		$result = $sgql->query($query);

		$this->assertEquals($expected, $result);
	}

	public function testSimpleInsertMultipleValues() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->insert([
				'customers' => [
					[
						'name' => 'New customer',
					],
					[
						'name' => 'New customer 2',
					]
				]
			]);

		$expected = [
			'values' => [
				'customers' => [
					[
						'id' => '5',
						'name' => 'New customer',
					],
					[
						'id' => '6',
						'name' => 'New customer 2',
					],
				],
			]
		];

		$result = $sgql->query($query);

		$this->assertEquals($expected, $result);
	}

	public function testInsertNestedValues() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->insert([
				'customers' => [
					[
						'name' => 'New customer',
						'orders' => [
							[
								'cost' => 84.2,
							],
						],
					],
					[
						'name' => 'New customer 2',
					],
				]
			]);

		$expected = [
			'values' => [
				'customers' => [
					[
						'id' => '5',
						'name' => 'New customer',
						'orders' => [
							[
								'id' => 4,
								'cost' => 84.2,
							],
						],
					],
					[
						'id' => '6',
						'name' => 'New customer 2',
					],
				]
			]
		];

		$result = $sgql->query($query);

		$this->assertEquals($expected, $result);

		$driver = $sgql->getDriver();
		$results = $driver->query("SELECT p_id, c_id FROM `sgql_association_1`")->fetchAll();

		$this->assertEquals([
			[ // Existing association
				'p_id' => 1,
				'c_id' => 1,
			],
			[ // New association
				'p_id' => 5,
				'c_id' => 4,
			]
		], $results);
	}

	public function testInsertComplex() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->insert(self::$complexInsert);

		$expected = [
			'values' => [
				'orders' => [
					[
						'id' => 4,
						'cost' => 5.5,
						'customers' => [
							[
								'id' => 5,
								'name' => 'Customer 1',
								'passports' => [
									[
										'id' => 1,
										'country' => 'US',
									]
								],
							],
						],
						'products' => [
							[
								'id' => '1',
								'name' => 'Product 1',
								'price' => 5.5
							],
						],
					],
					[
						'id' => 5,
						'cost' => 13.2,
						'customers' => [
							[
								'id' => 6,
								'name' => 'Customer 2',
								'passports' => [
									[
										'id' => 2,
										'country' => 'UK',
									]
								]
							]
						],
						'products' => [
							[
								'id' => 2,
								'name' => 'Product 3',
								'price' => 3.2
							],
							[
								'id' => 3,
								'name' => 'Product 2',
								'price' => 10
							],
						],
					],
				]
			]
		];

		$result = $sgql->query($query);

		$this->assertEquals($expected, $result);

		$driver = $sgql->getDriver();

		$results = $query = $driver->query("SELECT COUNT(*) as `count` FROM `sgql_association_1`")->fetchAll();
		$this->assertEquals(3, $results[0]['count']); // 1 was already there + 2 newly created

		$results = $query = $driver->query("SELECT COUNT(*) as `count` FROM `sgql_association_2`")->fetchAll();
		$this->assertEquals(3, $results[0]['count']);

		$results = $query = $driver->query("SELECT COUNT(*) as `count` FROM `sgql_association_3`")->fetchAll();
		$this->assertEquals(2, $results[0]['count']);
	}

	public function testInsertException() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->insert([
				'orders' => [
					[
						'cost' => 5.5,
						'customers' => [
							[
								'name' => 'Customer 1',
								'passports' => [
									[
										'issuer' => 'US', // issuer is an invalid field
									]
								],
							],
						],
					]
				]
			]);


		try {
			$sgql->query($query);
			$this->fail("Expected invalid column exception");
		} catch (Exception $e) {
			$this->assertEquals(
				"There was an error executing your query in namespace 'orders.customers.passports': " .
				"SQLSTATE[42S22]: Column not found: 1054 Unknown column 'issuer' in 'field list'",
				$e->getMessage()
			);
		}
	}
}