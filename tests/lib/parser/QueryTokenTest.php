<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class QueryTokenTest extends Parser_TestCase {
    public function testValidSelect() {
        require 'fixtures/validSelect1.php';

        $parser = new Parser($input, Parser::TOKEN_QUERY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidSelectWithWhere() {
        require 'fixtures/validSelectWithWhere1.php';

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
            ['SELECT `tests`:[`col`] WHERE `tests`:`col` == 2', "Expected ':('", 36],
            ['SELECT `tests`:[`col`] WHERE `tests`:(`col` == 2) AND `contacts`:()', "Invalid location aggregation or entity name",  66],
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

    public function testValidInsert() {
        require 'fixtures/validInsert1.php';

        $parser = new Parser($input, Parser::TOKEN_QUERY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidInsertProvider() {
        return [
            ['INSERT`tests`',               'Whitespace expected',      6],
            ['INSERT `tests`',              'Expected \':[\'',          14],
            ['INSERT `tests`:[]',           'Invalid entity name',      16],
            ['INSERT `tests`:[COUNT()]',    'Expected \']\'',           21],
            ['INSERT `tests`:[`col`',       'Expected \']\'',           22],
            ['INSERT `tests`:[`col]',       'Missing closing backtick', 16],
            ['INSERT `tests`:[`col`] VALUES :[]',               'Invalid entity name',  30],
            ['INSERT `tests`:[`col`] VALUES `schema`:[`col`]',  "Expected ':['",        45],
        ];
    }

    /**
     * @dataProvider invalidInsertProvider
     */
    public function testInvalidInsert($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_QUERY);
            $this->fail("Expected invalid insert exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }
}
