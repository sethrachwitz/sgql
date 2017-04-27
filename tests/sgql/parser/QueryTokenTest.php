<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class QueryTokenTest extends Parser_TestCase {
	public function testValidCreate() {
		require 'fixtures/validCreate1.php';

		$parser = new Parser($input, Parser::TOKEN_QUERY);
		$result = $parser->getParsed();

		$this->assertEquals($expected, $result);
	}

	public function testValidDestroy() {
		require 'fixtures/validDestroy1.php';

		$parser = new Parser($input, Parser::TOKEN_QUERY);
		$result = $parser->getParsed();

		$this->assertEquals($expected, $result);
	}

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

    public function testValidSelectWithWhereOrder() {
        require 'fixtures/validSelectWithWhereOrder1.php';

        $parser = new Parser($input, Parser::TOKEN_QUERY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidSelectWithWhereOrderShow() {
        require 'fixtures/validSelectWithWhereOrderShow1.php';

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
        } catch (\Exception $e) {
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

    public function testValidInsertAssociate() {
        require 'fixtures/validInsert2.php';

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
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }

    public function testValidUpdate() {
        require 'fixtures/validUpdate1.php';

        $parser = new Parser($input, Parser::TOKEN_QUERY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function testValidUpdateWithAssociateDisassociate() {
        require 'fixtures/validUpdate2.php';

        $parser = new Parser($input, Parser::TOKEN_QUERY);
        $result = $parser->getParsed();

        $this->assertEquals($expected, $result);
    }

    public function invalidUpdateProvider() {
        return [
            ['UPDATE`customers` WHERE `customers`(`id` == 4) SET vip = true',   "Whitespace expected",          6],
            ['UPDATE `customers` WHERE `customers`(`id` == 4) SET vip = true',  "Expected ':('",                36],
            ['UPDATE `customers` WHERE `customers`:(`id`) SET vip = true',      "Whitespace expected",          42],
            ['UPDATE `customers` WHERE `customers`:(`id` == 4)',                "Expected 'DISASSOCIATE'",      49],
            ['UPDATE `customers` SET vip = true',                               "Expected 'WHERE'",             19],
            ['UPDATE `customers` WHERE `customers`:(`id` == 4) SET vip = true,',"Invalid entity name",          65],
        ];
    }

    /**
     * @dataProvider invalidUpdateProvider
     */
    public function testInvalidUpdate($input, $message, $cursor) {
        try {
            $parser = new Parser($input, Parser::TOKEN_QUERY);
            $this->fail("Expected invalid update exception");
        } catch (\Exception $e) {
            $this->assertExceptionMessageEquals([
                'message' => $message,
                'cursor' => $cursor,
                'currentString' => substr($input, $cursor),
            ], $e->getMessage());
        }
    }
}
