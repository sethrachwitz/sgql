<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class SetsTokenTest extends Parser_TestCase {
    public function testValidSets() {
        require 'fixtures/validSets1.php';

        $parser = new Parser($input, Parser::TOKEN_SETS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidSetsProvider() {
        return [
            ['col1 == 5, col2 = "string"',  "Expected assignment operator", 5],
            ['col#1 == 5, col2 = "string"', "Invalid entity name",          0],
            ['col1 = 5,col2 = "string"',    "Whitespace expected",          9],
            ['col1 = 5, col2 == "string"',  "Expected assignment operator", 15],
        ];
    }

    /**
     * @dataProvider invalidSetsProvider
     */
    public function testInvalidSets($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_SETS);
            $this->fail("Expected invalid sets exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidSetsWithMultipleTokensInInput() {
        require 'fixtures/validSets1.php';

        $input .= ' ASSOCIATE'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_SETS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidSetsWithMultipleTokensInInput() {
        $input = 'col1 = 5, col2 == "string" ASSOCIATE `schema2`:`id` = 1';

        try {
            $parser = new Parser($input, Parser::TOKEN_SETS);
            $this->fail("Expected invalid sets exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected assignment operator",
                'cursor' => 15,
                'currentString' => substr($input, 15),
            ], $e->getMessage());
        }
    }
}
