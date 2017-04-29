<?php

namespace SGQL\Lib\Graph;

use SGQL\Lib\Drivers\MySQL;
use SGQL\MySQL_Database_TestCase;

include_once(dirname(__FILE__).'/../../../src/lib/graph/graph.php');
include_once(dirname(__FILE__).'/../../MySQL_Database_TestCase.php');

class GraphTest extends MySQL_Database_TestCase {
	protected $fixture = [
		[
			'default1Wireframe.sql',
			'default1Data.sql'
		],
		true
	];

    public function testGraphSchemas() {
		$driver = new MySQL(self::$hosts);
		$driver->useDatabase('sgql_unittests_data_1');

        $graph = new Graph($driver);
        $graph->initialize();

        $schema = $graph->getSchema('customers');
        $this->assertEquals('customers', $schema->getName());

        // Check that sgql_* tables are not included in the graph
		try {
			$graph->getSchema('sgql_info');
			$this->fail("Expected schema does not exist exception");
		} catch (\Exception $e) {
			$this->assertEquals("Schema 'sgql_info' does not exist", $e->getMessage());
		}
    }

    public function testGetNamespaceExists() {
		$driver = new MySQL(self::$hosts);
		$driver->useDatabase('sgql_unittests_data_1');

		$graph = new Graph($driver);
		$graph->initialize();

		$customersSchema = $graph->getSchema('customers');
		$ordersSchema = $graph->getSchema('orders');
		$productsSchema = $graph->getSchema('products');

		$graph->createAssociation($customersSchema, $ordersSchema, Association::TYPE_MANY_TO_ONE);
		$graph->createAssociation($ordersSchema, $productsSchema, Association::TYPE_MANY_TO_MANY);

        $namespace = [
            'customers',
            'orders',
            'products',
        ];

        $expected = [
            new Association (
            	$customersSchema,
				$ordersSchema,
                Association::TYPE_MANY_TO_ONE,
				1
            ),
            new Association (
            	$ordersSchema,
				$productsSchema,
                Association::TYPE_MANY_TO_MANY,
				2
            ),
        ];

        $this->assertEquals($expected, $graph->getNamespace($namespace));
    }

    public function testGetNamespaceDoesntExist() {
		$driver = new MySQL(self::$hosts);
		$driver->useDatabase('sgql_unittests_data_1');

		// Test with closed graph
        $graph = new Graph($driver);
        $graph->initialize();

		$customersSchema = $graph->getSchema('customers');
		$productsSchema = $graph->getSchema('products');

        $namespace = [
            'customers',
            'products',
        ];

        try {
            $graph->getNamespace($namespace);
            $this->fail("Expected namespace does not exist exception");
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), "Namespace 'customers.products' does not exist");
        }

        // Now test with an open graph
        $graph = new Graph($driver);
        $graph->setMode(Graph::MODE_OPEN);

        $expected = [
            new Association (
            	$customersSchema,
				$productsSchema,
                Association::TYPE_MANY_TO_MANY,
				1
            )
        ];

        $this->assertEquals($expected, $graph->getNamespace($namespace));
    }
}
