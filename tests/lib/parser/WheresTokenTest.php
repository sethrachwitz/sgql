<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class WheresTokenTest extends Parser_TestCase {
    public function testValidWheresSingleNamespace() {
        require 'fixtures/validWheres1.php';

        $parser = new Parser($input, Parser::TOKEN_WHERES);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidWheresMultipleNamespaces() {
        require 'fixtures/validWheres2.php';

        $parser = new Parser($input, Parser::TOKEN_WHERES);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidWheresProvider() {
        return [
            ['customers:(name == "Steve") AND orders :(COUNT(schema) > 2)',         "Expected ':('",                                38],
            ['customers.orders:(id == 5) AND customers:(SUM(orders. cost) > 20)',   "Whitespace expected",                          45],
            ['customers.orders:() AND customers:name == "Steve"',                   "Invalid location aggregation or entity name",  18],
        ];
    }

    /**
     * @dataProvider invalidWheresProvider
     */
    public function testInvalidWheres($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_WHERES);
            $this->fail("Expected invalid wheres exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidWheresWithMultipleTokensInInput() {
        require 'fixtures/validWheres1.php';

        $input .= ' ORDER'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_WHERES);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidWheresWithMultipleTokensInInput() {
        $input = 'schema1:(COUNT(schema2) > 2 AND HAS(schema3:id == 2) == true) AND schema1.schema2(COUNT(schema3) > 2) ORDER';

        try {
            $parser = new Parser($input, Parser::TOKEN_WHERES);
            $this->fail("Expected invalid wheres exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected ':('",
                'cursor' => 81,
                'currentString' => substr($input, 81),
            ], $e->getMessage());
        }
    }
}
