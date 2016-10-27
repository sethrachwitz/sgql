<?php

namespace SGQL;

use SGQL\Lib\Config as Config;

include_once(dirname(__FILE__).'/../../../../src/sgql.php');
include_once(dirname(__FILE__).'/../../../../src/sgql/query/query.php');

class SelectTest extends Config\Config_TestCase {
    public function testExportSelect() {
        $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
            ->select([
                'orders' => [
                    'id',
                    'price' => 'cost',
                    'customers' => [
                        'id',
                        'name',
                    ],
                ],
            ]);

        $stringQuery = new Query("SELECT `orders`:[`id`,`cost` AS `price`,`customers`:[`id`,`name`]]", self::$dataGraph, self::$driver);

        $expected = [
            'orders' => [
                Query::PART_COLUMNS => [
                    'id',
                    'price' => 'cost',
                ],
                'namespaces' => [
                    'customers' => [
                        Query::PART_COLUMNS => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expected, $chainedQuery->export());
    }

    public function testSelectColumnDoesNotExist() {
        try {
            $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
                ->select([
                    'orders' => [
                        'id',
                        'cost',
                        'customers' => [
                            'id',
                            'country',
                        ],
                    ],
                ]);
            $this->fail("Expected column does not exist exception");
        } catch (\Exception $e) {
            $this->assertEquals("Column 'orders.customers:country' does not exist", $e->getMessage());
        }

        $stringQuery = new Query("SELECT `orders`:[`id`,`cost`,`customers`:[`id`,`country`]]", self::$dataGraph, self::$driver);
    }

    public function testSelectNamespaceDoesNotExist() {
        try {
            $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
                ->select([
                    'orders' => [
                        'id',
                        'cost',
                        'customers' => [
                            'id',
                            'name',
                            'passports' => [
                                'id',
                                'date',
                            ],
                        ],
                    ],
                ]);
            $this->fail("Expected namespace does not exist exception");
        } catch (\Exception $e) {
            $this->assertEquals("Namespace 'orders.customers.passports' does not exist", $e->getMessage());
        }

        $stringQuery = new Query("SELECT `orders`:[`id`,`cost`,`customers`:[`id`,`name`,`passports`:[`id`,`date`]]]", self::$dataGraph, self::$driver);
    }

    public function testSelectColumnNameAlreadyUsed() {
        try {
            $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
                ->select([
                    'orders' => [
                        'id',
                        'cost',
                        'customers' => [
                            'id',
                            'name',
                            'name' => 'vip',
                        ],
                    ],
                ]);

                print_r($chainedQuery->export());
            $this->fail("Expected column name already used exception");
        } catch (\Exception $e) {
            $this->assertEquals("The column name 'orders.customers:name' has already been used", $e->getMessage());
        }

        $stringQuery = new Query("SELECT `orders`:[`id`,`cost`,`customers`:[`id`,`name`,`vip` AS `name`]]", self::$dataGraph, self::$driver);
    }

    public function testSelectNoColumnsForSchema() {
        try {
            $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
                ->select([
                    'orders' => [
                        'customers' => [
                            'id',
                            'name',
                        ],
                    ],
                ]);

            $this->fail("Expected no columns exception");
        } catch (\Exception $e) {
            $this->assertEquals("No columns specified for namespace 'orders'", $e->getMessage());
        }

        $stringQuery = new Query("SELECT `orders`:[`customers`:[`id`,`name`]]", self::$dataGraph, self::$driver);
    }

    public function testSelectNoColumnsOrRelationshipsForSchema() {
        try {
            $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
                ->select([
                    'orders' => [
                        'id',
                        'cost',
                        'customers' => [
                            // Empty
                        ],
                    ],
                ]);

            $this->fail("Expected nothing is defined exception");
        } catch (\Exception $e) {
            $this->assertEquals("Nothing is defined for namespace 'orders.customers'", $e->getMessage());
        }

        $stringQuery = new Query("SELECT `orders`:[`customers`:[`id`,`name`]]", self::$dataGraph, self::$driver);
    }

    public function testSelectNoNamespaces() {
        try {
            $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
                ->select([

                ]);

            $this->fail("Expected no namespace exception");
        } catch (\Exception $e) {
            $this->assertEquals("No namespace specified", $e->getMessage());
        }

        // Not possible to build a query string with no namespaces
    }

    public function testSelectEmptyNamespace() {
        try {
            $chainedQuery = (new Query(null, self::$dataGraph, self::$driver))
                ->select([
                    [

                    ],
                ]);

            $this->fail("Expected no namespaces exception");
        } catch (\Exception $e) {
            $this->assertEquals("One or more sets of columns is not referenced by a schema name", $e->getMessage());
        }

        // Not possible to build a query string with an empty namespace
    }
}
