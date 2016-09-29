<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class EntityAssignTokenTest extends Parser_TestCase {
    public function testValidEntityAssign() {
        require 'fixtures/validEntityAssign1.php';

        $parser = new Parser($input, Parser::TOKEN_ENTITY_ASSIGN);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidEntityAssignProvider() {
        return [
            ['col1 == 5',           "Expected assignment operator", 5],
            ['col#1 == 5',          "Invalid entity name",          0],
            ['col2="string"',       "Invalid entity name",          0],
            ['col2 = = "string"',   "Invalid value",                7],
        ];
    }

    /**
     * @dataProvider invalidEntityAssignProvider
     */
    public function testInvalidEntityAssign($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_ENTITY_ASSIGN);
            $this->fail("Expected invalid entity assign exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidEntityAssignWithMultipleTokensInInput() {
        require 'fixtures/validEntityAssign1.php';

        $input .= ', col2 = 15.9'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_ENTITY_ASSIGN);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidEntityAssignWithMultipleTokensInInput() {
        $input = 'col1 => 5, col2 = "string"';

        try {
            $parser = new Parser($input, Parser::TOKEN_ENTITY_ASSIGN);
            $this->fail("Expected invalid entity assign exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected assignment operator",
                'cursor' => 5,
                'currentString' => substr($input, 5),
            ], $e->getMessage());
        }
    }
}
