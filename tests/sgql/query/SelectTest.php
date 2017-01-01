<?php

include_once(dirname(__FILE__).'/../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../SGQL_MySQL_Database_TestCase.php');

class SelectTest extends SGQL_MySQL_Database_TestCase {
	protected $fixture = [
		[
			'default1Wireframe.sql',
			'default1Data.sql'
		],
		true
	];

	public function testOnlyColumnsSelect() {
		$sgql = new SGQL(self::$config, self::$database);
		$query = $sgql->newQuery()
			->select([
				'customers' => [
					'id',
					'name',
				]
			]);
	}

	public function simpleSelectTest() {
		$sgql = new SGQL(self::$config, self::$database);
		$query = $sgql->newQuery()
			->select([
				'customers' => [
					'id',
					'name',
					'orders' => [
						'id',
						'price',
					],
				],
			]);
	}
}