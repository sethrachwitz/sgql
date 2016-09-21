<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class NamespaceTokenTest extends Parser_TestCase {
    public function testValidNamespace() {
        require 'fixtures/validNamespace1.php';

        $parser = new Parser($input, Parser::TOKEN_NAMESPACE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testSingleSchemaNamespace() {
        require 'fixtures/validNamespace2.php';

        $parser = new Parser($input, Parser::TOKEN_NAMESPACE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidNamespaceProvider() {
        return [
            ['`schema*`',           'Invalid entity name',  0],
            ['``',                  'Invalid entity name',  0],
            ['',                    'Invalid entity name',  0],
            ['`schema`.`schem&`',   'Invalid entity name',  9],
            ['.`schema`',           'Invalid entity name',  0],
        ];
    }

    /**
     * @dataProvider invalidNamespaceProvider
     */
    public function testInvalidNamespace($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_NAMESPACE);
            $this->fail("Expected invalid namespace exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidNamespaceWithMultipleTokensInInput() {
        require 'fixtures/validNamespace1.php';

        $input .= ':`column`'; // Add another valid token to the input

        $parser = new Parser($input, Parser::TOKEN_NAMESPACE);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testInvalidNamespaceWithMultipleTokensInInput() {
        $input = '`schema`.`sch(ema`:`column`';

        try {
            $parser = new Parser($input, Parser::TOKEN_NAMESPACE);
            $this->fail("Expected invalid namespace exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Missing closing backtick',
                'cursor' => 9,
                'currentString' => substr($input, 9),
            ], $e->getMessage());
        }
    }
}
