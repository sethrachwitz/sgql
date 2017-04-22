<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ValueGraphsTokenTest extends Parser_TestCase {
    public function testValidValueGraphs() {
        require 'fixtures/validValueGraphs1.php';

        $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPHS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidValueGraphsProvider() {
        return [
            ['`test`:["value"],``col^`',            'Invalid entity name',          17],
            ['`test`:["value"],`test`:["string]',   'Invalid entity name or value', 25],
            ['`test`:["string",],`test`:["value"]', 'Invalid entity name or value', 17],
            ['`test`:["value"],``:[]',              'Invalid entity name',          17],
            ['`test`:["value"],:[]',                'Invalid entity name',          17],
            ['`test`:[`column`,],`test`:["value"]', "Expected ':['",                16],
	        ['`test`:[`column`,],',                 "Expected ':['",                16],
        ];
    }

    /**
     * @dataProvider invalidValueGraphsProvider
     */
    public function testInvalidValueGraphs($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPHS);
            $this->fail("Expected invalid value graphs exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidValueGraphsWithMultipleTokensInInput() {
        require 'fixtures/validValueGraphs1.php';

        $input .= ' ASSOCIATE'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPHS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidValueGraphsWithMultipleTokensInInput() {
        $input = '`customers`:[false, 12.3],`customers`:[12:[false, 55.98]] ASSOCIATE';

        try {
            $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPHS);
            $this->fail("Expected invalid value graphs exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected ']'",
                'cursor' => 41,
                'currentString' => substr($input, 41),
            ], $e->getMessage());
        }
    }
}
