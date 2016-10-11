<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class OrderByTokenTest extends Parser_TestCase {
    public function testValidOrderBy() {
        require 'fixtures/validOrderBy1.php';

        $parser = new Parser($input, Parser::TOKEN_ORDER_BY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidOrderByProvider() {
        return [
            ['customers by name ASC',   "Expected 'BY'",            10],
            ['orders ASC',              "Expected 'BY'",            7],
            ['orders BY ASC',           "Invalid order direction",  14],
            ['customers.orders ASC',    "Expected 'BY'",            17],
        ];
    }

    /**
     * @dataProvider invalidOrderByProvider
     */
    public function testInvalidOrderBy($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_ORDER_BY);
            $this->fail("Expected invalid order by exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidOrderByWithMultipleTokensInInput() {
        require 'fixtures/validOrderBy1.php';

        $input .= ', customers BY name ASC'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_ORDER_BY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidOrderByWithMultipleTokensInInput() {
        $input = 'customers BY name ASCENDING, customers.orders';

        try {
            $parser = new Parser($input, Parser::TOKEN_ORDER_BY);
            $this->fail("Expected invalid order by exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Invalid order direction",
                'cursor' => 18,
                'currentString' => substr($input, 18),
            ], $e->getMessage());
        }
    }
}
