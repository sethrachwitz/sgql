<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ColumnCompareTokenTest extends Parser_TestCase {
    public function testValidColumnCompare() {
        require 'fixtures/validColumnCompare1.php';

        $parser = new Parser($input, Parser::TOKEN_COLUMN_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidColumnCompareProvider() {
        return [
            ['col1 == 5',                   "Expected ':'",         4],
            ['schema:col#1 == 5',           "Invalid entity name",  7],
            ['s&chema:col2 = "string"',     "Invalid entity name",  0],
            ['schema:col2 == > "string"',   "Invalid value",        15],
        ];
    }

    /**
     * @dataProvider invalidColumnCompareProvider
     */
    public function testInvalidColumnCompare($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_COLUMN_COMPARE);
            $this->fail("Expected invalid column compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidColumnCompareWithMultipleTokensInInput() {
        require 'fixtures/validColumnCompare1.php';

        $input .= ', schema:col2 >= 15.9'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_COLUMN_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidColumnCompareWithMultipleTokensInInput() {
        $input = 'schema:col1 = 5, schema:col2 >= "string"';

        try {
            $parser = new Parser($input, Parser::TOKEN_COLUMN_COMPARE);
            $this->fail("Expected invalid column compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected comparison operator",
                'cursor' => 12,
                'currentString' => substr($input, 12),
            ], $e->getMessage());
        }
    }
}
