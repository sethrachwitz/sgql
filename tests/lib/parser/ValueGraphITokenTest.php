<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ValueGraphITokenTest extends Parser_TestCase {
    public function testValidValueGraphI() {
        require 'fixtures/validValueGraphI1.php';

        $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH_I);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidValueGraphIProvider() {
        return [
            ['12, `col^`',                  'Invalid entity name or value', 4],
            ['"string",`test`:["string]',   'Invalid entity name or value', 17],
            ['`test`:["string",]',          'Invalid entity name or value', 17],
            ['``:[]',                       'Invalid entity name or value', 0],
            [':[]',                         'Invalid entity name or value', 0],
            ['`test`:[`column`,]',          "Expected ':['",                16],
        ];
    }

    /**
     * @dataProvider invalidValueGraphIProvider
     */
    public function testInvalidValueGraphI($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH_I);
            $this->fail("Expected invalid value graph I exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidValueGraphIWithMultipleTokensInInput() {
        require 'fixtures/validValueGraphI1.php';

        $input .= '],55'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH_I);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidValueGraphIWithMultipleTokensInInput() {
        $input = '`schema`:[12&, "string"]],15]';

        try {
            $parser = new Parser($input, Parser::TOKEN_VALUE_GRAPH_I);
            $this->fail("Expected invalid value graph I exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name or value',
                'cursor' => 10,
                'currentString' => substr($input, 10),
            ], $e->getMessage());
        }
    }
}
