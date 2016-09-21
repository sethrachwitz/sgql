<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class AggregationFunctionNameTokenTest extends Parser_TestCase {
    public function validAggregationFunctionNameProvider() {
        return [
            ['SUM'],
            ['AVERAGE'],
            ['MEAN'],
            ['MEDIAN'],
            ['MIN'],
            ['MAX'],
        ];
    }

    /**
     * @dataProvider validAggregationFunctionNameProvider
     */
    public function testValidAggregationFunctionName($input) {
        $parser = new Parser($input, Parser::TOKEN_AGGREGATION_FUNCTION_NAME);
        $result = $parser->getParsed();

        $this->assertEquals([
            'value' => $input,
            'location' => 0
        ], $result);
    }

    public function testInvalidAggregationFunctionName() {
        $input = 'COUNT';

        try {
            $parser = new Parser($input, Parser::TOKEN_AGGREGATION_FUNCTION_NAME);
            $this->fail("Expected invalid aggregation function exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid aggregation function',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }

    public function testValidAggregationFunctionNameWithMultipleTokensInInput() {
        $input = 'SUM(`entity`)';

        $parser = new Parser($input, Parser::TOKEN_AGGREGATION_FUNCTION_NAME);
        $result = $parser->getParsed();

        $this->assertEquals([
            'value' => 'SUM',
            'location' => 0
        ], $result);
    }

    public function testInvalidAggregationFunctionNameWithMultipleTokensInInput() {
        $input = 'COUNT(`entity`)';

        try {
            $parser = new Parser($input, Parser::TOKEN_AGGREGATION_FUNCTION_NAME);
            $this->fail("Expected invalid aggregation function exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid aggregation function',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }
}
