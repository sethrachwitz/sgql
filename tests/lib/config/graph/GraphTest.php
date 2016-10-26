<?php

namespace SGQL\Lib\Config;

include_once(dirname(__FILE__).'/../../../../src/lib/config/graph/graph.php');
include_once(dirname(__FILE__).'/../../../Config_TestCase.php');

class GraphTest extends Config_TestCase {
    public function testGraphSchemas() {
        $graph = new Graph(self::$config['graphs']['sgql_unittests_data_[1|2]']);

        $schema = $graph->getSchema('customers');
        $this->assertEquals('customers', $schema->getName());
        $this->assertEquals(3, count($schema->getColumns()));
    }

    public function testGetNamespaceExists() {
        $graph = new Graph(self::$config['graphs']['sgql_unittests_data_[1|2]']);

        $namespace = [
            'customers',
            'orders',
            'products',
        ];

        $expected = [
            new Relationship (
                'customers',
                'orders',
                '<-'
            ),
            new Relationship (
                'orders',
                'products',
                '<->'
            ),
        ];

        $this->assertEquals($expected, $graph->getNamespace($namespace));
    }

    public function testGetNamespaceDoesntExist() {
        // Test with closed graph
        $graph = new Graph(self::$config['graphs']['sgql_unittests_data_[1|2]']);

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
        $openGraph = self::$config['graphs']['sgql_unittests_data_[1|2]'];
        $openGraph['mode'] = 'open';

        $graph = new Graph($openGraph);

        $expected = [
            new Relationship (
                'customers',
                'products',
                '<->'
            )
        ];

        $this->assertEquals($expected, $graph->getNamespace($namespace));
    }
}
