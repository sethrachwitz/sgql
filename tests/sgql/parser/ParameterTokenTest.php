<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ParameterTokenTest extends Parser_TestCase {
    public function validParameterProvider() {
        return [
            ['?parameter'],
            ['?Parameter'],
            ['?Param1'],
        ];
    }

    /**
     * @dataProvider validParameterProvider
     */
    public function testValidParameter($input) {
        $parser = new Parser($input, Parser::TOKEN_PARAMETER);
        $result = $parser->getParsed();

        $this->assertEquals([
            'type' => Parser::TOKEN_PARAMETER,
            'value' => substr($input, 1),
            'location' => 0
        ], $result);
    }

    public function invalidParameterProvider() {
        return [
            ['?&param'],
            ['??param'],
            ['?param*'],
            ['param'],
        ];
    }

    /**
     * @dataProvider invalidParameterProvider
     */
    public function testInvalidParameter($input) {
        try {
            $parser = new Parser($input, Parser::TOKEN_PARAMETER);
            $this->fail("Expected invalid parameter exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid parameter name',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }
}
