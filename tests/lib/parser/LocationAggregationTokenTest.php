<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class LocationAggregationTokenTest extends Parser_TestCase {
    public function testValidLocationAggregation() {
        require 'fixtures/validLocationAggregation1.php';

        $parser = new Parser($input, Parser::TOKEN_LOCATION_AGGREGATION);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidLocationAggregationProvider() {
        return [
            ['COUNT(`schema`:`column`)',    'Invalid aggregation function', 0],
            ['SUM(`sch&ema`:`column`)',     'Invalid entity name',          4],
            ['COUNT(`schema`)',             'Invalid aggregation function', 0],
            ['SUM(``)',                     'Invalid entity name',          4],
            ['SUM()',                       'Invalid entity name',          4],
            ['COUNT(`schema`)',             'Invalid aggregation function', 0],
        ];
    }

    /**
     * @dataProvider invalidLocationAggregationProvider
     */
    public function testInvalidLocationAggregation($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION_AGGREGATION);
            $this->fail("Expected invalid location aggregation exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidLocationAggregationWithMultipleTokensInInput() {
        require 'fixtures/validLocationAggregation1.php';

        $input .= ',`schema`'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_LOCATION_AGGREGATION);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidLocationAggregationWithMultipleTokensInInput() {
        $input = 'SUM(`enti%ty`),`schema`';

        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION_AGGREGATION);
            $this->fail("Expected invalid location aggregation exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 4,
                'currentString' => substr($input, 4),
            ], $e->getMessage());
        }
    }
}
