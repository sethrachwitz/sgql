<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ValueGraphTokenTest extends Parser_TestCase {
    public function testValidValueGraph() {
        require 'fixtures/validValueGraph1.php';

        $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidValueGraphProvider() {
        return [
            ['`col^`',              'Invalid entity name',          0],
            ['`test`:["string]',    'Invalid entity name or value', 8],
            ['`test`:["string",]',  'Invalid entity name or value', 17],
            ['``:[]',               'Invalid entity name',          0],
            [':[]',                 'Invalid entity name',          0],
            ['`test`:[`column`,]',  "Expected ':['",                16],
        ];
    }

    /**
     * @dataProvider invalidValueGraphProvider
     */
    public function testInvalidValueGraph($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH);
            $this->fail("Expected invalid value graph exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidValueGraphWithMultipleTokensInInput() {
        require 'fixtures/validValueGraph1.php';

        $input .= ' ASSOCIATE'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidValueGraphWithMultipleTokensInInput() {
        $input = '`customers`:[12:[false, 55.98]] ASSOCIATE';

        try {
            $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH);
            $this->fail("Expected invalid value graph exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected ']'",
                'cursor' => 15,
                'currentString' => substr($input, 15),
            ], $e->getMessage());
        }
    }
}
