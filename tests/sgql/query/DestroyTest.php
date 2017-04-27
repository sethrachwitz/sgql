<?php

include_once(dirname(__FILE__).'/../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../SGQL_MySQL_Database_TestCase.php');

class DestroyTest extends SGQL_MySQL_Database_TestCase {
	protected $fixture = [
		[
			'SGQLSetup.sql',
			'default1Wireframe.sql',
			'default1SGQL.sql',
		],
		true
	];

	public function testDestroyAssociation() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$this->assertCount(1, $sgql->getGraph()->getAssociations());

		$query = $sgql->newQuery()
			->destroyAssociation('customers', 'orders');
		$sgql->query($query);

		$this->assertCount(0, $sgql->getGraph()->getAssociations());
	}

	public function testDestroyAssociationStringQuery() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$this->assertCount(1, $sgql->getGraph()->getAssociations());

		$query = $sgql->newQuery("DESTROY ASSOCIATION `customers` <- `orders`");
		$sgql->query($query);

		$this->assertCount(0, $sgql->getGraph()->getAssociations());
	}

	public function testDestroyNonExistingAssociation() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->destroyAssociation('orders', 'products');

		try {
			$sgql->query($query);
			$this->fail("Expected association does not exist exception");
		} catch (Exception $e) {
			$this->assertEquals("Association between 'orders' and 'products' was not found", $e->getMessage());
		}
	}

	public function testDestroyAssociationStringWrongType() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery("DESTROY ASSOCIATION `customers` - `orders`");;

		try {
			$sgql->query($query);
			$this->fail("Expected wrong association type exception");
		} catch (Exception $e) {
			$this->assertEquals("Association between 'customers' and 'orders' is of type '<-'", $e->getMessage());
		}
	}
}