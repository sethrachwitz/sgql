<?php

namespace SGQL\Lib\Query;

include_once(dirname(__FILE__).'/../../../../src/lib/query/mysql.php');

class SelectTest extends \PHPUnit_Framework_TestCase {
    public function testSelect() {
        $expected = 'SELECT `orders`.`id`,`orders`.`cost`,`orders`.`shipped` FROM `orders`;';

        $query = (new MySQL())
            ->select([
                'orders' => [
                    'id',
                    'cost',
                    'shipped',
                ],
            ])
            ->from('orders');

        $this->assertEquals($expected, $query->toString());
    }

    public function testSelectColumnsFrom() {
        $expected = 'SELECT `orders`.`id`,`orders`.`cost` AS `price`,`orders`.`shipped` FROM `orders`;';

        $query = (new MySQL())
            ->select([
                'orders' => [
                    'id',
                    'price' => 'cost',
                    'shipped',
                ],
            ])
            ->from('orders');

        $this->assertEquals($expected, $query->toString());
    }

    public function testSelectMultipleTables() {
        $expected = 'SELECT `customers`.`id`,`customers`.`name`,`orders`.`id`,`orders`.`customer`,`orders`.`cost`,`orders`.`shipped` FROM `orders`;';

        $query = (new MySQL())
            ->select([
                'customers' => [
                    'id',
                    'name',
                ],
                'orders' => [
                    'id',
                    'customer',
                    'cost',
                    'shipped',
                ],
            ])
            ->from('orders');

        $this->assertEquals($expected, $query->toString());
    }

    public function testSelectLeftJoin() {
        $expected = 'SELECT `customers`.`id`,`customers`.`name`,`orders`.`id`,`orders`.`customer`,`orders`.`cost`,`orders`.`shipped` FROM `orders` LEFT JOIN `customers` ON customers.id = orders.customer;';

        $query = (new MySQL())
            ->select([
                'customers' => [
                    'id',
                    'name',
                ],
                'orders' => [
                    'id',
                    'customer',
                    'cost',
                    'shipped',
                ],
            ])
            ->from('orders')
            ->join('customers', 'customers.id = orders.customer', MySQL::LEFT_JOIN);

        $this->assertEquals($expected, $query->toString());
    }

    public function testSelectWhere() {
        $expected = 'SELECT `orders`.`id`,`orders`.`cost`,`orders`.`shipped` FROM `orders` WHERE cost > 200 AND shipped != 1;';

        $query = (new MySQL())
            ->select([
                'orders' => [
                    'id',
                    'cost',
                    'shipped',
                ],
            ])
            ->from('orders')
            ->where('cost > 200')
            ->where('shipped != 1');

        $this->assertEquals($expected, $query->toString());
    }

    public function testSelectJoinWhere() {
        $expected = 'SELECT `customers`.`id`,`orders`.`id`,`orders`.`cost` FROM `orders` LEFT JOIN `customers` ON customers.id = orders.customer WHERE cost > :cost;';

        $query = (new MySQL())
            ->select([
                'customers' => [
                    'id',
                ],
                'orders' => [
                    'id',
                    'cost'
                ],
            ])
            ->from('orders')
            ->join('customers', 'customers.id = orders.customer', MySQL::LEFT_JOIN)
            ->where('cost > :cost');

        $this->assertEquals($expected, $query->toString());
    }

    public function testRequiredClauses() {
        $query = (new MySQL())
            ->select([
                'customers' => [
                    'id',
                ],
            ]);

        try {
            $query->toString();
            $this->fail("Expected error requiring from");
        } catch (\Exception $e) {
            $this->assertEquals("Missing FROM clause", $e->getMessage());
        }
    }
}
