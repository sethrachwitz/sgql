<?php

namespace SGQL\Lib\Config;

include_once(dirname(__FILE__).'/../../../../src/lib/config/graph/graph.php');
include_once(dirname(__FILE__).'/../../../Config_TestCase.php');

class SchemaTest extends Config_TestCase {
    public function testSchema() {
        $schema = new Schema('customers', self::$config['graphs']['sgql_unittests_data_[1|2]']['schemas']['customers']);

        $this->assertEquals('customers', $schema->getName());
        $this->assertEquals(3, count($schema->getColumns()));
    }

    public function testGetPrimaryColumn() {
        $schema = new Schema('customers', self::$config['graphs']['sgql_unittests_data_[1|2]']['schemas']['customers']);

        $expected = new Column (
            'id',
            [
                'type' => 'integer',
                'index' => 'primary',
            ]
        );

        $this->assertEquals($expected, $schema->getPrimaryColumn());
    }

    public function testColumnExists() {
        $schema = new Schema('customers', self::$config['graphs']['sgql_unittests_data_[1|2]']['schemas']['customers']);

        $this->assertEquals(true, $schema->columnExists('id'));
    }
}
