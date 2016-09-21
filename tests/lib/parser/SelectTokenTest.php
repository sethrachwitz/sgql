<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class SelectTokenTest extends Parser_TestCase {
    public function testValidSelect() {
        require 'fixtures/validSelect1.php';

        $parser = new Parser($input, Parser::TOKEN_QUERY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidSelectProvider() {
        return [
            ['SELECT`tests`',               'Whitespace expected',      6],
            ['SELECT `tests`',              'Expected \':[\'',          14],
            ['SELECT `tests`:[]',           'Invalid entity name',      16],
            ['SELECT `tests`:[COUNT()]',    'Expected \']\'',           21],
            ['SELECT `tests`:[`col`',       'Expected \']\'',           22],
            ['SELECT `tests`:[`col]',       'Missing closing backtick', 16],
        ];
    }

    /**
     * @dataProvider invalidSelectProvider
     */
    public function testInvalidSelect($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_QUERY);
            $this->fail("Expected invalid select exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }
}
