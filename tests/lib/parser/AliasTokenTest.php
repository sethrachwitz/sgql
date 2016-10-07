<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class AliasTokenTest extends Parser_TestCase {
    public function validAliasProvider() {
        return [
            ['AS test',         'test'],
            ['AS `test`',       'test'],
            ['AS 123test',      '123test'],
            ['AS `123test`',    '123test'],
            ['AS Name_1',       'Name_1'],
            ['AS $Name_1',      '$Name_1'],
        ];
    }

    /**
     * @dataProvider validAliasProvider
     */
    public function testValidAlias($input, $value) {
        $parser = new Parser($input, Parser::TOKEN_ALIAS);
        $result = $parser->getParsed();

        $this->assertEquals([
            'type' => Parser::TOKEN_ALIAS,
            'value' => $value,
            'withBackticks' => '`'.$value.'`',
            'location' => 0
        ], $result);
    }

    public function invalidAliasProvider() {
        return [
            ['AS ',     4, "Invalid entity name",   ""],
            ['test',    0, "Expected 'AS'",         "test"],
            ['AS`test`',2, "Whitespace expected",   "`test`"],
            ['``',      0, "Expected 'AS'",         "``"],
            ['',        0, "Expected 'AS'",         ""]
        ];
    }

    /**
     * @dataProvider invalidAliasProvider
     */
    public function testInvalidAlias($input, $cursor, $exception, $currentString) {
        try {
            $parser = new Parser($input, Parser::TOKEN_ALIAS);
            $this->fail("Expected invalid alias exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $exception,
                'cursor' => $cursor,
                'currentString' => $currentString,
            ], $e->getMessage());
        }
    }
}
