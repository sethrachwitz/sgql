<?php

include_once(dirname(__FILE__).'/../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../SGQL_MySQL_Database_TestCase.php');

class CreateTest extends SGQL_MySQL_Database_TestCase {
	protected $fixture = [
		[
			'SGQLSetup.sql',
			'default1Wireframe.sql',
			'default1SGQL.sql',
		],
		true
	];

	public function testCreateAssociation() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$this->assertCount(1, $sgql->getGraph()->getAssociations());

		$query = $sgql->newQuery()
			->createAssociation('orders', SGQL::ASSOCIATION_TYPE_MANY_TO_MANY, 'products');

		/** @var \SGQL\Lib\Graph\Association $association */
		$association = $sgql->query($query);

		$this->assertEquals('orders', $association->getParent()->getName());
		$this->assertEquals('products', $association->getChild()->getName());
		$this->assertEquals(2, $association->getType());

		$this->assertCount(2, $sgql->getGraph()->getAssociations());
	}

	public function testCreateAssociationStringQuery() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$this->assertCount(1, $sgql->getGraph()->getAssociations());

		$query = $sgql->newQuery("CREATE ASSOCIATION `orders` <-> `products`");

		/** @var \SGQL\Lib\Graph\Association $association */
		$association = $sgql->query($query);

		$this->assertEquals('orders', $association->getParent()->getName());
		$this->assertEquals('products', $association->getChild()->getName());
		$this->assertEquals(2, $association->getType());

		$this->assertCount(2, $sgql->getGraph()->getAssociations());
	}

	public function testCreateExistingAssociation() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->createAssociation('customers', SGQL::ASSOCIATION_TYPE_MANY_TO_ONE, 'orders');

		try {
			$sgql->query($query);
			$this->fail("Expected association already exists exception");
		} catch (Exception $e) {
			$this->assertEquals("Association between 'customers' and 'orders' already exists", $e->getMessage());
		}
	}

	public function testCreateAssociationInvalidSchema() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		try {
			$query = $sgql->newQuery()
				->createAssociation('customers', SGQL::ASSOCIATION_TYPE_MANY_TO_ONE, 'invalid');
			$this->fail("Expected invalid schema exception");
		} catch (Exception $e) {
			$this->assertEquals("Schema 'invalid' does not exist", $e->getMessage());
		}
	}

	public function testCreateAssociationInvalidType() {
		$sgql = new SGQL(self::$config, self::$database);
		$sgql->initialize();

		$query = $sgql->newQuery()
			->createAssociation('orders', '< - >', 'products');

		try {
			$sgql->query($query);
			$this->fail("Expected invalid type exception");
		} catch (Exception $e) {
			$this->assertEquals("Invalid association type '< - >'", $e->getMessage());
		}
	}
}