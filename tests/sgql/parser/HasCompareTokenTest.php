<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class HasCompareTokenTest extends Parser_TestCase {
    public function testValidHasCompare() {
        require 'fixtures/validHasCompare1.php';

        $parser = new Parser($input, Parser::TOKEN_HAS_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidHasCompareProvider() {
        return [
            ['HAS(`id` == 5) == true',  "Expected ':'",                 8],
            ['HAS (test) == false',     "Expected 'HAS('",              0],
            ['HAS(schema:col )',        'Expected comparison operator', 15],
            ['HAS(schema:col== 2)',     'Invalid entity name',          11],
        ];
    }

    /**
     * @dataProvider invalidHasCompareProvider
     */
    public function testInvalidHasCompare($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_HAS_COMPARE);
            $this->fail("Expected invalid has compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidHasCompareWithMultipleTokensInInput() {
        require 'fixtures/validHasCompare1.php';

        $input .= ') == true'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_HAS_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidHasCompareWithMultipleTokensInInput() {
        $input = 'HAS(schema > 5) == 5 AND shipped != true';

        try {
            $parser = new Parser($input, Parser::TOKEN_HAS_COMPARE);
            $this->fail("Expected invalid has compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected ':'",
                'cursor' => 10,
                'currentString' => substr($input, 10),
            ], $e->getMessage());
        }
    }
}
