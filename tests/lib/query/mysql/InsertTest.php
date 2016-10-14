<?php

namespace SGQL\Lib\Query;

include_once(dirname(__FILE__).'/../../../../src/lib/query/mysql.php');

class InsertTest extends \PHPUnit_Framework_TestCase {
    public function testInsert() {
        $expectedQuery = 'INSERT INTO `orders` (`cost`,`shipped`) VALUES (:r0c0,:r0c1);';
        $expectedData = [
            'r0c0' => 37.9,
            'r0c1' => 0,
        ];

        $query = (new MySQL())
            ->insert('orders')
            ->values([
                [
                    'cost' => 37.9,
                    'shipped' => 0,
                ],
            ]);

        $this->assertEquals($expectedQuery, $query->toString());
        $this->assertEquals($expectedData, $query->getData());
    }

    public function testInsertMultipleValues() {
        $expectedQuery = 'INSERT INTO `orders` (`cost`,`shipped`) VALUES (:r0c0,:r0c1),(:r1c0,:r1c1),(:r2c0,:r2c1);';
        $expectedData = [
            'r0c0' => 37.9,
            'r0c1' => 0,
            'r1c0' => 22.15,
            'r1c1' => 1,
            'r2c0' => 91.3,
            'r2c1' => 0,
        ];

        $query = (new MySQL())
            ->insert('orders')
            ->values([
                [
                    'cost' => 37.9,
                    'shipped' => 0,
                ],
                [
                    'cost' => 22.15,
                    'shipped' => 1,
                ],
                [
                    'cost' => 91.3,
                    'shipped' => 0,
                ],
            ]);

        $this->assertEquals($expectedQuery, $query->toString());
        $this->assertEquals($expectedData, $query->getData());
    }

    public function testInsertRequiredClauses() {
        $query = (new MySQL())
            ->insert('orders');

        try {
            $query->toString();
            $this->fail("Expected error requiring values");
        } catch (\Exception $e) {
            $this->assertEquals("Missing VALUES clause", $e->getMessage());
        }
    }
}
