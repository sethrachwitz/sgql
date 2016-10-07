<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class CompareTokenTest extends Parser_TestCase {
    public function testValidCompareSimple() {
        require 'fixtures/validCompare1.php';

        $parser = new Parser($input, Parser::TOKEN_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidCompareHasIn() {
        require 'fixtures/validCompare2.php';

        $parser = new Parser($input, Parser::TOKEN_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidCompareCount() {
        require 'fixtures/validCompare3.php';

        $parser = new Parser($input, Parser::TOKEN_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidCompareAggregation() {
        require 'fixtures/validCompare4.php';

        $parser = new Parser($input, Parser::TOKEN_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidCompareProvider() {
        return [
            ['COUNT(`schema`) IN 2',        "Use of 'IN' is limited to non-aggregation comparisons",    16],
            ['SUM(`schema`) == 2',          "Whitespace expected",                                      3],
            ['HAS(`orders`.`id`) IN 2',     'Whitespace expected',                                      3],
            ['HAS',                         'Expected comparison operator',                             4],
        ];
    }

    /**
     * @dataProvider invalidCompareProvider
     */
    public function testInvalidCompare($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_COMPARE);
            $this->fail("Expected invalid compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidCompareWithMultipleTokensInInput() {
        require 'fixtures/validCompare1.php';

        $input .= ' AND HAS(schema:`id` == 5) == true'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidCompareWithMultipleTokensInInput() {
        $input = 'COUNT(schema) IN 2 AND HAS(schema2:id == 2)';

        try {
            $parser = new Parser($input, Parser::TOKEN_COMPARE);
            $this->fail("Expected invalid compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Use of 'IN' is limited to non-aggregation comparisons",
                'cursor' => 14,
                'currentString' => substr($input, 14),
            ], $e->getMessage());
        }
    }
}
