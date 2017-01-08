<?php

include_once(dirname(__FILE__).'/../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../SGQL_MySQL_Database_TestCase.php');

class SelectTest extends SGQL_MySQL_Database_TestCase {
	protected $fixture = [
		[
			'SGQLSetup.sql',
			'default1Wireframe.sql',
			'default1Data.sql',
			'default1SGQL.sql',
		],
		true
	];

	public function testCreateQueryForUnititializedDatabase() {
		$sgql = new SGQL(self::$config, 'sgql_unittests_data_2');
		try {
			$query = $sgql->newQuery();
			$this->fail("Expected can't create query for uninitialized databases exception");
		} catch (\Exception $e) {
			$this->assertEquals("Can't create queries for databases that haven't been initialized for SGQL", $e->getMessage());
		}
	}

	public function testOnlyColumnsSelect() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->select([
				'customers' => [
					'id',
					'name',
				]
			]);

		$expected = [
			'customers' => [
				[
					'id' => 1,
					'name' => 'Steve Jobs',
				],
				[
					'id' => 2,
					'name' => 'Larry Ellison',
				],
				[
					'id' => 3,
					'name' => 'Mark Zuckerburg',
				],
				[
					'id' => 4,
					'name' => 'Jack Dorsey',
				],
			]
		];

		$result = $sgql->query($query);

		$this->assertEquals($expected, $result);
	}

	public function testNamespaceSelect() {
		$sgql = new SGQL(self::$config, self::$database);

		$query = $sgql->newQuery()
			->select([
				'customers' => [
					'id',
					'name',
					'orders' => [
						'id',
						'cost',
					],
				],
			]);

		$expected = [
			'customers' => [
				[
					'id' => 1,
					'name' => 'Steve Jobs',
					'orders' => [
						[
							'id' => 1,
							'cost' => 22.5
						],
					],
				],
				[
					'id' => 2,
					'name' => 'Larry Ellison',
					'orders' => [],
				],
				[
					'id' => 3,
					'name' => 'Mark Zuckerburg',
					'orders' => [],
				],
				[
					'id' => 4,
					'name' => 'Jack Dorsey',
					'orders' => [],
				],
			]
		];

		$result = $sgql->query($query);

		$this->assertEquals($expected, $result);
	}

	public function testNamespaceSelectPrimaryColumnNotPartOfQuery() {
		$sgql = new SGQL(self::$config, self::$database);

		$query = $sgql->newQuery()
			->select([
				'customers' => [
					'name',
					'orders' => [
						'cost',
					],
				],
			]);

		$expected = [
			'customers' => [
				[
					'name' => 'Steve Jobs',
					'orders' => [
						[
							'cost' => 22.5
						],
					],
				],
				[
					'name' => 'Larry Ellison',
					'orders' => [],
				],
				[
					'name' => 'Mark Zuckerburg',
					'orders' => [],
				],
				[
					'name' => 'Jack Dorsey',
					'orders' => [],
				],
			]
		];

		$result = $sgql->query($query);

		$this->assertEquals($expected, $result);
	}
}