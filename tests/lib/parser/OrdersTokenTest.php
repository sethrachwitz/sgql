<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class OrdersTokenTest extends Parser_TestCase {
    public function testValidOrders() {
        require 'fixtures/validOrders1.php';

        $parser = new Parser($input, Parser::TOKEN_ORDERS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidOrdersProvider() {
        return [
            ['customers by name ASC, orders BY cost DESC',  "Expected 'BY'",            10],
            ['orders ASC, customers BY name DESC',          "Expected 'BY'",            7],
            ['orders BY name',                              "Invalid order direction",  15],
            ['customers BY name ASC, customers.orders ASC', "Expected 'BY'",            40],
        ];
    }

    /**
     * @dataProvider invalidOrdersProvider
     */
    public function testInvalidOrders($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_ORDERS);
            $this->fail("Expected invalid orders exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidOrdersWithMultipleTokensInInput() {
        require 'fixtures/validOrders1.php';

        $input .= ' SHOW'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_ORDERS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidOrdersWithMultipleTokensInInput() {
        $input = 'customers BY name ASC, customers.orders DESC SHOW';

        try {
            $parser = new Parser($input, Parser::TOKEN_ORDERS);
            $this->fail("Expected invalid orders exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected 'BY'",
                'cursor' => 40,
                'currentString' => substr($input, 40),
            ], $e->getMessage());
        }
    }
}
