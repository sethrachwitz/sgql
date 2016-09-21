<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class LocationGraphTokenTest extends Parser_TestCase {
    public function testValidLocationGraph() {
        require 'fixtures/validLocationGraph1.php';

        $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidLocationGraphProvider() {
        return [
            ['`col^`',              'Invalid entity name',  0],
            ['`test`:[`co@lumn`]',  'Invalid entity name',  8],
            ['``:[]',               'Invalid entity name',  0],
            [':[]',                 'Invalid entity name',  0],
            ['`test`:[`column`,]',  'Invalid entity name',  17],
        ];
    }

    /**
     * @dataProvider invalidLocationGraphProvider
     */
    public function testInvalidLocationGraph($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH);
            $this->fail("Expected invalid location graph exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidLocationGraphWithMultipleTokensInInput() {
        require 'fixtures/validLocationGraph1.php';

        $input .= ' WHERE'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidLocationGraphWithMultipleTokensInInput() {
        $input = '`customers`:[`schema`:[col&, col2]] WHERE';

        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION_GRAPH);
            $this->fail("Expected invalid location graph exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 23,
                'currentString' => substr($input, 23),
            ], $e->getMessage());
        }
    }
}
