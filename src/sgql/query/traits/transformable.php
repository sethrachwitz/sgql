<?php

namespace SGQL;

trait Transformable {
    protected function transform($input) {
        $this->parser = new Parser($input);
        $parsed = $this->parser->getParsed();

        if (isset($parsed[Parser::KEYWORD_SELECT])) {
            $this->transformSelect($parsed);
        }
    }

    private function transformSelect(array $parsed) {
        // Doesn't do anything yet, but this will transform a query string that was parsed into what $this->select needs
    }
}
