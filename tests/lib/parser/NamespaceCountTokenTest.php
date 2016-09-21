<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class NamespaceCountTokenTest extends Parser_TestCase {
    public function testValidNamespaceCount() {
        require 'fixtures/validNamespaceCount1.php';

        $parser = new Parser($input, Parser::TOKEN_NAMESPACE_COUNT);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidNamespaceCountProvider() {
        return [
            ['COUNT(`schema`:`column`)',    'Expected \')\'',           14],
            ['SUM(`schema`:`column`)',      'Invalid count function',   0],
            ['COUNT(`schema*`)',            'Invalid entity name',      6],
            ['COUNT(``)',                   'Invalid entity name',      6],
            ['COUNT()',                     'Invalid entity name',      6],
            ['SUM(`schema`)',               'Invalid count function',   0],
        ];
    }

    /**
     * @dataProvider invalidNamespaceCountProvider
     */
    public function testInvalidNamespaceCount($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_NAMESPACE_COUNT);
            $this->fail("Expected invalid namespace count exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidNamespaceCountWithMultipleTokensInInput() {
        require 'fixtures/validNamespaceCount1.php';

        $input .= ',`schema`'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_NAMESPACE_COUNT);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidNamespaceCountWithMultipleTokensInInput() {
        $input = 'COUNT(`enti%ty`),`schema`';

        try {
            $parser = new Parser($input, Parser::TOKEN_NAMESPACE_COUNT);
            $this->fail("Expected invalid namespace count exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 6,
                'currentString' => substr($input, 6),
            ], $e->getMessage());
        }
    }
}
