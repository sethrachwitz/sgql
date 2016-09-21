<?php

include_once(dirname(__FILE__).'/../src/lib/parser/parser.php');

class Parser_TestCase extends PHPUnit_Framework_TestCase {
    public function assertExceptionMessageEquals(array $expected, $actual) {
        if (!isset($expected['message']) || !isset($expected['currentString']) || !isset($expected['cursor'])) {
            $this->fail('Bad parameters');
        }

        $currentStringLength = strlen($expected['currentString']);

        // Trim current string to tidbit
        if ($currentStringLength >= Parser::EXCEPTION_TIDBIT_LENGTH) {
            $actualCurrentString = substr($expected['currentString'], 0, Parser::EXCEPTION_TIDBIT_LENGTH).
                ($currentStringLength >= Parser::EXCEPTION_TIDBIT_LENGTH + 1 ? '...' : '');
        } else {
            $actualCurrentString = $expected['currentString'];
        }

        $exception = $expected['message'].' at "'.$actualCurrentString.'" (Index '.$expected['cursor'].')';

        $this->assertEquals($exception, $actual);
    }
}
