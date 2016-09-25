<?php

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class EntityNameTokenTest extends Parser_TestCase {
    public function validEntityNameProvider() {
        return [
            ['name',    'name'],
            ['name1',   'name1'],
            ['Name',    'Name'],
            ['Name1',   'Name1'],
            ['Name_1',  'Name_1'],
            ['$Name_1', '$Name_1'],
        ];
    }

    /**
     * @dataProvider validEntityNameProvider
     */
    public function testValidEntityName($input, $value) {
        $parser = new Parser($input, Parser::TOKEN_ENTITY_NAME);
        $result = $parser->getParsed();

        $this->assertEquals([
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => $value,
            'withBackticks' => '`'.$value.'`',
            'location' => 0
        ], $result);
    }

    /**
     * @dataProvider validEntityNameProvider
     */
    public function testValidEntityNameWithBackticks($input, $value) {
        $parser = new Parser('`'.$input.'`', Parser::TOKEN_ENTITY_NAME);
        $result = $parser->getParsed();

        $this->assertEquals([
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => $value,
            'withBackticks' => '`'.$value.'`',
            'location' => 0
        ], $result);
    }

    public function invalidEntityNameProvider() {
        return [
            ['&test'],
            ['test&'],
            ['te&st'],
            ['&'],
            ['`'],
            ['']
        ];
    }

    /**
     * @dataProvider invalidEntityNameProvider
     */
    public function testInvalidEntityName($input) {
        try {
            $parser = new Parser($input, Parser::TOKEN_ENTITY_NAME);
            $this->fail("Expected invalid entity exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }

    public function validEntityNameWithMultipleTokensInInputProvider() {
        return [
            ['name,test',    'name'],
            ['name1,test',   'name1'],
            ['Name,test',    'Name'],
            ['Name1,test',   'Name1'],
            ['Name_1,test',  'Name_1'],
            ['$Name_1,test', '$Name_1'],
        ];
    }

    /**
     * @dataProvider validEntityNameWithMultipleTokensInInputProvider
     */
    public function testValidEntityNameWithMultipleTokensInInput($input, $value) {
        $parser = new Parser($input, Parser::TOKEN_ENTITY_NAME);
        $result = $parser->getParsed();

        $this->assertEquals([
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => $value,
            'withBackticks' => '`'.$value.'`',
            'location' => 0
        ], $result);
    }

    public function invalidEntityNameWithMultipleTokensInInputProvider() {
        return [
            ['&test,test'],
            ['test&,test'],
            ['te&st,test'],
            ['&,test'],
        ];
    }

    /**
     * @dataProvider invalidEntityNameWithMultipleTokensInInputProvider
     */
    public function testInvalidEntityNameWithMultipleTokensInInput($input) {
        try {
            $parser = new Parser($input, Parser::TOKEN_ENTITY_NAME);
            $this->fail("Expected invalid entity exception");
        } catch (Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => 'Invalid entity name',
                'cursor' => 0,
                'currentString' => $input,
            ], $e->getMessage());
        }
    }
}
