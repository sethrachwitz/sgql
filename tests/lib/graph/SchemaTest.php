<?php

namespace SGQL\Lib\Graph;

include_once(dirname(__FILE__).'/../../../src/lib/graph/schema.php');

class SchemaTest extends \PHPUnit_Framework_TestCase {
    public function testSchema() {
        $schema = new Schema(0, 'customers', 'id');

        $this->assertEquals(0, $schema->getId());
        $this->assertEquals('customers', $schema->getName());
        $this->assertEquals('id', $schema->getPrimaryColumn());
    }
}
