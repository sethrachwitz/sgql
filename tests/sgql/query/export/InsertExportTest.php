<?php

namespace SGQL;

use SGQL\Lib\Graph as Graph;
use SGQL\Lib\Drivers\MySQL;

include_once(dirname(__FILE__).'/../../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../../Graph_MySQL_Database_TestCase.php');

class InsertExportTest extends Graph_MySQL_Database_TestCase {
	protected $fixture = [
		[
			'default1Wireframe.sql',
		],
		true
	];

	public function testExportInsert() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
			->insert([
				'customers' => [
					[
						'name' => 'Larry Ellison',
					],
				]
			]);

		$stringQuery = new Query('INSERT `customers`:[`name`] VALUES `customers`:["Larry Ellison"]', self::$graph, self::$driver);

		$expected = [
			Query::PART_VALUES => [
				'customers' => [
					[
						Query::PART_COLUMNS => [
							'name' => 'Larry Ellison',
						],
					],
				],
			],
		];

		$this->assertEquals($expected, $chainedQuery->export());
		$this->assertEquals($expected, $stringQuery->export());
	}

	public function testExportInsertMultipleValues() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
			->insert([
				'customers' => [
					[
						'name' => 'Larry Ellison',
					],
					[
						'name' => 'Bill Gates',
					],
				]
			]);

		$stringQuery = new Query('INSERT `customers`:[`name`] VALUES `customers`:["Larry Ellison"],`customers`:["Bill Gates"]', self::$graph, self::$driver);

		$expected = [
			Query::PART_VALUES => [
				'customers' => [
					[
						Query::PART_COLUMNS => [
							'name' => 'Larry Ellison',
						],
					],
					[
						Query::PART_COLUMNS => [
							'name' => 'Bill Gates',
						],
					],
				],
			],
		];

		$this->assertEquals($expected, $stringQuery->export());
		$this->assertEquals($expected, $chainedQuery->export());
	}

	public function testExportInsertNestedValue() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
			->insert([
				'customers' => [
					[
						'name' => 'Larry Ellison',
						'orders' => [
							[
								'cost' => 92.3,
							]
						],
					],
				]
			]);

		$stringQuery = new Query('INSERT `customers`:[`name`,`orders`:[`cost`]] VALUES `customers`:["Larry Ellison",`orders`:[92.3]]', self::$graph, self::$driver);

		$expected = [
			Query::PART_VALUES => [
				'customers' => [
					[
						Query::PART_COLUMNS => [
							'name' => 'Larry Ellison',
						],
						'namespaces' => [
							'orders' => [
								[
									Query::PART_COLUMNS => [
										'cost' => 92.3
									],
								],
							],
						],
					],
				],
			],
		];

		$this->assertEquals($expected, $stringQuery->export());
		$this->assertEquals($expected, $chainedQuery->export());
	}

	public function testExportInsertMultipleNestedValues() {
		$chainedQuery = (new Query(null, self::$graph, self::$driver))
			->insert([
				'customers' => [
					[
						'name' => 'Larry Ellison',
						'orders' => [
							[
								'cost' => 92.3,
							]
						],
					],
					[
						'name' => 'Bill Gates',
						'orders' => [
							[
								'cost' => 19.22,
							],
							[
								'cost' => 13.5,
							],
						],
					],
				]
			]);

		// String queries do not allow multiple nested values for readability

		$expected = [
			Query::PART_VALUES => [
				'customers' => [
					[
						Query::PART_COLUMNS => [
							'name' => 'Larry Ellison',
						],
						'namespaces' => [
							'orders' => [
								[
									Query::PART_COLUMNS => [
										'cost' => 92.3
									],
								],
							],
						],
					],
					[
						Query::PART_COLUMNS => [
							'name' => 'Bill Gates',
						],
						'namespaces' => [
							'orders' => [
								[
									Query::PART_COLUMNS => [
										'cost' => 19.22
									],
								],
								[
									Query::PART_COLUMNS => [
										'cost' => 13.5
									],
								],
							],
						],
					],
				],
			],
		];

		$this->assertEquals($expected, $chainedQuery->export());
	}

	public function testInsertNoNamespaces() {
		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->insert([

				]);
			$this->fail("Expected no namespace exception");
		} catch (\Exception $e) {
			$this->assertEquals("No namespace specified", $e->getMessage());
		}
	}

	public function testInsertNoSchemaName() {
		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->insert([
					[

					],
				]);
			$this->fail("Expected no top level schema name exception");
		} catch (\Exception $e) {
			$this->assertEquals("One or more sets of values is not referenced by a schema name", $e->getMessage());
		}
	}

	public function testInsertNoNestedSchemaName() {
		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->insert([
					'customers' => [
						[
							'name' => 'Larry Ellison',
							[
								[
									'cost' => 12.3,
								],
							],
						],
					],
				]);
			$this->fail("Expected no schema name for nested records exception");
		} catch (\Exception $e) {
			$this->assertEquals("One or more sets of values is not referenced by a schema name", $e->getMessage());
		}
	}

	public function testInsertEmptyRecord() {
		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->insert([
					'customers' => [
						[
							'name' => 'Bill Gates',
							'orders' => [
								[

								],
							],
						],
					],
				]);
			$this->fail("Expected records cannot be empty exception");
		} catch (\Exception $e) {
			$this->assertEquals("No values specified for namespace 'customers.orders'", $e->getMessage());
		}
	}

	public function testInsertSingleRecordNotInArray() {
		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->insert([
					'customers' => [
						'name' => 'Bill Gates', // Should be in its own array representing a single record
					],
				]);
			$this->fail("Expected records must be in arrays exception");
		} catch (\Exception $e) {
			$this->assertEquals("Invalid value structure for 'customers'", $e->getMessage());
		}
	}

	public function testInsertMultipleTopLevelSchemas() {
		try {
			$chainedQuery = (new Query(null, self::$graph, self::$driver))
				->insert([
					'customers' => [
						[
							'name' => 'Mark Zuckerburg',
						],
					],
					'orders' => [
						[
							'cost' => 12.5,
						],
					],
				]);
			$this->fail("Expected multiple top level schemas exception");
		} catch (\Exception $e) {
			$this->assertEquals("Only one top level schema can be specified", $e->getMessage());
		}
	}

	public function testInsertNoColumnForValue() {
		try {
			$stringQuery = new Query('INSERT `customers`:[`name`,`orders`:[`cost`]] VALUES `customers`:[`orders`:[92.3],"Larry Ellison"]', self::$graph, self::$driver);
			$this->fail("Expected no column for value exception");
		} catch (\Exception $e) {
			$this->assertEquals("No column for value 'Larry Ellison'", $e->getMessage());
		}
	}

	public function testInsertNoColumnForValue2() {
		try {
			$stringQuery = new Query('INSERT `customers`:[`name`] VALUES `customers`:[`orders`:[92.3]]', self::$graph, self::$driver);
			$this->fail("Expected unable to associate schema with value exception");
		} catch (\Exception $e) {
			$this->assertEquals("Unable to associate schema 'orders' with a value", $e->getMessage());
		}
	}

	public function testInsertStringColumnValueCountMismatch() {
		try {
			$stringQuery = new Query('INSERT `customers`:[`name`,`vip`] VALUES `customers`:["Larry Ellison"]', self::$graph, self::$driver);
			$this->fail("Expected column / value count mismatch exception");
		} catch (\Exception $e) {
			$this->assertEquals("Column / value count mismatch in namespace 'customers'", $e->getMessage());
		}
	}
}