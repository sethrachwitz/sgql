<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class CountFunctionNameTokenTest extends Parser_TestCase {
    public function validCountFunctionNameProvider() {
        return [
            ['COUNT'],
        ];
    }

    /**
     * @dataProvider validCountFunctionNameProvider
     */
    public function testValidCountFunctionName($input) {
        $parser = new Parser($input, Parser::TOKEN_COUNT_FUNCTION_NAME);
        $result = $parser->getParsed();

        $this->assertEquals([
            'value' => $input,
            'location' => 0
        ], $result);
    }

    public function testInvalidCountFunctionName() {
        $input = 'SUM';

        try {
            $parser = new Parser($input, Parser::TOKEN_COUNT_FUNCTION_NAME);
            $this->fail("Expected invalid count function exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid count function',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }

    public function testValidCountFunctionNameWithMultipleTokensInInput() {
        $input = 'COUNT(`entity`)';

        $parser = new Parser($input, Parser::TOKEN_COUNT_FUNCTION_NAME);
        $result = $parser->getParsed();

        $this->assertEquals([
            'value' => 'COUNT',
            'location' => 0
        ], $result);
    }

    public function testInvalidCountFunctionNameWithMultipleTokensInInput() {
        $input = 'SUM(`entity`)';

        try {
            $parser = new Parser($input, Parser::TOKEN_COUNT_FUNCTION_NAME);
            $this->fail("Expected invalid count function exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid count function',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }
}
