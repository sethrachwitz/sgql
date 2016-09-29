<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class ColumnTokenTest extends Parser_TestCase {
    public function testValidColumn() {
        require 'fixtures/validColumn1.php';

        $parser = new Parser($input, Parser::TOKEN_COLUMN);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidColumnProvider() {
        return [
            ['`schema*`:`column`',          'Invalid entity name',  0],
            ['``:`column`',                 'Invalid entity name',  0],
            [':`column`',                   'Invalid entity name',  0],
            ['`schema`.`schema`:`co!umn`',  "Expected ':'",         8],
            ['`schema`.`2`.:`column`',      "Expected ':'",         8],
        ];
    }

    /**
     * @dataProvider invalidColumnProvider
     */
    public function testInvalidColumn($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_COLUMN);
            $this->fail("Expected invalid column exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidColumnWithMultipleTokensInInput() {
        require 'fixtures/validColumn1.php';

        $input .= ', `schema`:`column2`'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_COLUMN);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidColumnWithMultipleTokensInInput() {
        $input = '`schema2`:`co!umn`, `schema2`:`column2`';

        try {
            $parser = new Parser($input, Parser::TOKEN_COLUMN);
            $this->fail("Expected invalid column exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 10,
                'currentString' => substr($input, 10),
            ], $e->getMessage());
        }
    }
}
