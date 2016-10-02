<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ShowITokenTest extends Parser_TestCase {
    public function testValidShowISchemaPage() {
        require 'fixtures/validShowI1.php';

        $parser = new Parser($input, Parser::TOKEN_SHOW_I);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidShowINamespace() {
        require 'fixtures/validShowI2.php';

        $parser = new Parser($input, Parser::TOKEN_SHOW_I);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidShowIProvider() {
        return [
            ['10 customers.',       "Invalid entity name",  13],
            ['15 customers PAGE',   "Invalid integer",      18],
            ['customers PAGE 12',   "Invalid integer",      0],
        ];
    }

    /**
     * @dataProvider invalidShowIProvider
     */
    public function testInvalidShowI($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_SHOW_I);
            $this->fail("Expected invalid show I exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidShowsWithMultipleTokensInInput() {
        require 'fixtures/validShowI1.php';

        $input .= ', 2 customers.orders'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_SHOW_I);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidShowsWithMultipleTokensInInput() {
        $input = '15 customers., -2 customers.orders';

        try {
            $parser = new Parser($input, Parser::TOKEN_SHOWS);
            $this->fail("Expected invalid show I exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Invalid entity name",
                'cursor' => 13,
                'currentString' => substr($input, 13),
            ], $e->getMessage());
        }
    }
}
