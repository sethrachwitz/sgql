<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class LocationGraphITokenTest extends Parser_TestCase {
    public function testValidLocationGraphI() {
        require 'fixtures/validLocationGraphI1.php';

        $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH_I);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidLocationGraphIProvider() {
        return [
            ['`col^`',                      'Invalid entity name',  0],
            ['`test`:[`co@lumn`]',          'Invalid entity name',  8],
            ['``:[]',                       'Invalid entity name',  0],
            [':[]',                         'Invalid entity name',  0],
            ['`test`:[`column`],,`col2`',   'Invalid entity name',  18],
        ];
    }

    /**
     * @dataProvider invalidLocationGraphIProvider
     */
    public function testInvalidLocationGraphI($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH_I);
            $this->fail("Expected invalid location graph I exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidLocationGraphIWithMultipleTokensInInput() {
        require 'fixtures/validLocationGraphI1.php';

        $input .= '] WHERE'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH_I);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidLocationGraphIWithMultipleTokensInInput() {
        $input = '`schema`:[col&, col2]] WHERE';

        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH_I);
            $this->fail("Expected invalid location graph I exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 10,
                'currentString' => substr($input, 10),
            ], $e->getMessage());
        }
    }
}
