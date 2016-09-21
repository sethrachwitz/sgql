<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class LocationTokenTest extends Parser_TestCase {
    public function testValidLocation() {
        require 'fixtures/validLocation1.php';

        $parser = new Parser($input, Parser::TOKEN_LOCATION);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidLocationProvider() {
        return [
            ['`schema*`:`column`',          'Invalid entity name',  0],
            ['``:`column`',                 'Invalid entity name',  0],
            [':`column`',                   'Invalid entity name',  0],
            ['`schema`.`schema`:`co!umn`',  'Invalid entity name',  18],
            ['`schema`.`2`.:`column`',      'Invalid entity name',  13],
        ];
    }

    /**
     * @dataProvider invalidLocationProvider
     */
    public function testInvalidLocation($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION);
            $this->fail("Expected invalid location exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidLocationWithMultipleTokensInInput() {
        require 'fixtures/validLocation1.php';

        $input .= ',`namespace2schema`.`schema2`'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_LOCATION);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidLocationWithMultipleTokensInInput() {
        $input = '`schema1`.`schema2`:`co!umn`,`namespace2schema`.`schema2`';

        try {
            $parser = new Parser($input, Parser::TOKEN_LOCATION);
            $this->fail("Expected invalid location exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 20,
                'currentString' => substr($input, 20),
            ], $e->getMessage());
        }
    }
}
