<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ComparesTokenTest extends Parser_TestCase {
    public function testValidCompares() {
        require 'fixtures/validCompares1.php';

        $parser = new Parser($input, Parser::TOKEN_COMPARES);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidComparesProvider() {
        return [
            ['COUNT(`schema`) == 2 AND id',     "Expected comparison operator",                 28],
            ['SUM(`schema`:count) == 2 AND',    "Invalid location aggregation or entity name",  29],
            ['schema AND HAS cost == 2',        'Expected comparison operator',                 7],
        ];
    }

    /**
     * @dataProvider invalidComparesProvider
     */
    public function testInvalidCompares($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_COMPARES);
            $this->fail("Expected invalid compares exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidComparesWithMultipleTokensInInput() {
        require 'fixtures/validCompares1.php';

        $input .= ') AND'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_COMPARES);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidComparesWithMultipleTokensInInput() {
        $input = 'COUNT(schema) == 2 AND HAS id IN 2';

        try {
            $parser = new Parser($input, Parser::TOKEN_COMPARES);
            $this->fail("Expected invalid compares exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Invalid parameter name",
                'cursor' => 33,
                'currentString' => substr($input, 33),
            ], $e->getMessage());
        }
    }
}
