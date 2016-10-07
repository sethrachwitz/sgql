<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class WhereCompareTokenTest extends Parser_TestCase {
    public function testValidWhereCompare() {
        require 'fixtures/validWhereCompare1.php';

        $parser = new Parser($input, Parser::TOKEN_WHERE_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidWhereCompareProvider() {
        return [
            ['orders :(COUNT(schema) > 2)',         "Expected ':('",        6],
            ['customers:(SUM(orders. cost) > 20)',  "Whitespace expected",  14],
            ['customers:name == "Steve"',           "Expected ':('",        9],
        ];
    }

    /**
     * @dataProvider invalidWhereCompareProvider
     */
    public function testInvalidWhereCompare($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_WHERE_COMPARE);
            $this->fail("Expected invalid where compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidWhereCompareWithMultipleTokensInInput() {
        require 'fixtures/validWhereCompare1.php';

        $input .= ' AND'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_WHERE_COMPARE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidWhereCompareWithMultipleTokensInInput() {
        $input = 'schema1:(COUNT(schema2) 2 AND HAS(schema2:id == 2) == true AND';

        try {
            $parser = new Parser($input, Parser::TOKEN_WHERE_COMPARE);
            $this->fail("Expected invalid where compare exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => "Expected comparison operator",
                'cursor' => 24,
                'currentString' => substr($input, 24),
            ], $e->getMessage());
        }
    }
}
