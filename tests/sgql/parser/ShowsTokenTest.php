<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ShowsTokenTest extends Parser_TestCase {
    public function testValidShows() {
        require 'fixtures/validShows1.php';

        $parser = new Parser($input, Parser::TOKEN_SHOWS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidShowsProvider() {
        return [
            ['10 customers, 15 customers.',         "Invalid entity name",  27],
            ['customers PAGE',                      "Invalid integer",      0],
            ['10 customers,15 customers.orders',    "Whitespace expected",  13],
        ];
    }

    /**
     * @dataProvider invalidShowsProvider
     */
    public function testInvalidShows($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_SHOWS);
            $this->fail("Expected invalid shows exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidShowsWithMultipleTokensInInput() {
        require 'fixtures/validShows1.php';

        $input .= ' TOKEN'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_SHOWS);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidShowsWithMultipleTokensInInput() {
        $input = '15 customers PAGE 10, -2 customers.orders TOKEN';

        try {
            $parser = new Parser($input, Parser::TOKEN_SHOWS);
            $this->fail("Expected invalid shows exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Integer must be positive",
                'cursor' => 22,
                'currentString' => substr($input, 22),
            ], $e->getMessage());
        }
    }
}
