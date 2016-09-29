<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class AssociatesTokenTest extends Parser_TestCase {
    public function testValidAssociates() {
        require 'fixtures/validAssociates1.php';

        $parser = new Parser($input, Parser::TOKEN_ASSOCIATES);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidAssociatesProvider() {
        return [
            ['col1 == 5, col2 == "string"',                 "Expected ':'",                 4],
            ['schema:col#1 == 5, col2 = "string"',          "Invalid entity name",          7],
            ['schema:col1 == 5,schema:col2 == "string"',    "Whitespace expected",          17],
            ['schema:col1 == 5, schema:col2 = "string"',    "Expected comparison operator", 30],
        ];
    }

    /**
     * @dataProvider invalidAssociatesProvider
     */
    public function testInvalidAssociates($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_ASSOCIATES);
            $this->fail("Expected invalid associates exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidAssociatesWithMultipleTokensInInput() {
        require 'fixtures/validAssociates1.php';

        $input .= ' DISASSOCIATE'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_ASSOCIATES);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidAssociatesWithMultipleTokensInInput() {
        $input = 'schema:col1 == 5, schema:col2 == "string DISASSOCIATE `schema2`:`id` = 1';

        try {
            $parser = new Parser($input, Parser::TOKEN_ASSOCIATES);
            $this->fail("Expected invalid associates exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Invalid value",
                'cursor' => 33,
                'currentString' => substr($input, 33),
            ], $e->getMessage());
        }
    }
}
