<?php

namespace SGQL;

include_once(dirname(__FILE__).'/../../Parser_TestCase.php');

class AssocTokenTest extends Parser_TestCase {
	public function testValidAssoc() {
		require 'fixtures/validAssoc1.php';

		$parser = new Parser($input, Parser::TOKEN_ASSOC);
		$result = $parser->getParsed();

		$this->assertEquals($expected, $result);
	}

	public function invalidAssocProvider() {
		return [
			['customers<- orders',          "Invalid entity name",      0],
			['customers < - > orders',      "Expected association type",  10],
			['customers -> orders',         "Expected association type",  10],
			['customers -',                 "Invalid entity name",  12],
			['<-',                          "Invalid entity name",        0],
		];
	}

	/**
	 * @dataProvider invalidAssocProvider
	 */
	public function testInvalidAssoc($input, $message, $cursor) {
		try {
			$parser = new Parser($input, Parser::TOKEN_ASSOC);
			$this->fail("Expected invalid assoc exception");
		} catch (\Exception $e) {
			$this->assertExceptionMessageEquals([
				'message' => $message,
				'cursor' => $cursor,
				'currentString' => substr($input, $cursor),
			], $e->getMessage());
		}
	}

	public function testValidAssocWithMultipleTokensInInput() {
		require 'fixtures/validAssoc1.php';

		$input .= ', '; // Not really valid, but it doesn't matter for this test

		$parser = new Parser($input, Parser::TOKEN_ASSOC);
		$result = $parser->getParsed();

		$this->assertEquals($expected, $result);
	}

	public function testInvalidAssocWithMultipleTokensInInput() {
		$input = '`orders` -> customers, ';

		try {
			$parser = new Parser($input, Parser::TOKEN_ASSOC);
			$this->fail("Expected invalid association type exception");
		} catch (\Exception $e) {
			$this->assertExceptionMessageEquals([
				'message' => "Expected association type",
				'cursor' => 9,
				'currentString' => substr($input, 9),
			], $e->getMessage());
		}
	}
}
