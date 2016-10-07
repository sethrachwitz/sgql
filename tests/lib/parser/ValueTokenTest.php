<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ValueTokenTest extends Parser_TestCase {
    public function validValueProvider() {
        return [
            ['0',           Parser::TOKEN_INTEGER],
            ['123',         Parser::TOKEN_INTEGER],
            ['0.0',         Parser::TOKEN_DOUBLE],
            ['1.15',        Parser::TOKEN_DOUBLE],
            ['true',        Parser::TOKEN_BOOLEAN],
            ['false',       Parser::TOKEN_BOOLEAN],
            ['"string"',    Parser::TOKEN_STRING],
            ['"12"',        Parser::TOKEN_STRING],
        ];
    }

    /**
     * @dataProvider validValueProvider
     */
    public function testValidValue($input, $type) {
        $parser = new Parser($input, Parser::TOKEN_VALUE);
        $result = $parser->getParsed();

        // Just check withquotes for each string, since the input will have quotes
        if (isset($result['withQuotes'])) {
            $result['value'] = $result['withQuotes'];
            unset($result['withQuotes']);
        }

        $this->assertEquals([
            'type' => $type,
            'value' => $input,
            'location' => 0
        ], $result);
    }

    public function invalidValueProvider() {
        return [
            ['1a2'],
            ['"asdf""'],
            ['1e16'],
        ];
    }

    /**
     * @dataProvider invalidValueProvider
     */
    public function testInvalidValue($input) {
        try {
            $parser = new Parser($input, Parser::TOKEN_VALUE);
            $this->fail("Expected invalid value exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid value',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }
}
