<?php

class Parser {
    // Keywords
    const TOKEN_QUERY = 'query';
    const KEYWORD_SELECT = 'SELECT';
    const KEYWORD_INSERT = 'INSERT';
    const KEYWORD_UPDATE = 'UPDATE';
    const KEYWORD_DELETE = 'DELETE';
    const KEYWORD_UNDELETE = 'UNDELETE';
    const KEYWORD_DESCRIBE = 'DESCRIBE';

    const KEYWORD_WHERE = 'WHERE';
    const TOKEN_WHERES = 'wheres';
    const TOKEN_WHERE_COMPARE = 'whereCompare';

    // Graphs
    const TOKEN_LOCATION_GRAPH = 'locationGraph';
    const TOKEN_LOCATION_GRAPH_I = 'locationGraphI';

    // Functions and their inputs
    const TOKEN_LOCATION_AGGREGATION = 'locationAggregation';
    const TOKEN_NAMESPACE_COUNT = 'namespaceCount';

    // Entities
    const TOKEN_ENTITY_NAME = 'entityName';
    const TOKEN_COLUMN = 'column';
    const TOKEN_SCHEMA = 'schema';
    const TOKEN_NAMESPACE = 'namespace';
    const TOKEN_LOCATION = 'location';

    // Function names
    const TOKEN_AGGREGATION_FUNCTION_NAME = 'aggregationFunctionName';
    const TOKEN_COUNT_FUNCTION_NAME = 'countFunctionName';

    // Aggregations
    const KEYWORD_SUM = 'SUM';
    const KEYWORD_AVERAGE = 'AVERAGE';
    const KEYWORD_MEAN = 'MEAN';
    const KEYWORD_MEDIAN = 'MEDIAN';
    const KEYWORD_MIN = 'MIN';
    const KEYWORD_MAX = 'MAX';
    const TOKEN_COUNT = 'COUNT';

    // Regex that determines when a token can be terminated (i.e., valid tokens will terminate with
    // one of these characters coming after it
    const TERMINATING_REGEX = '(?=[\s`,:.\(\[\)\]])';

    // Length of current position in the query to display in an exception
    const EXCEPTION_TIDBIT_LENGTH = 12;

    // Holds the query in its entirety
    private $query;

    // Holds the ending result of the parser
    private $parsed;

    public function getParsed() {
        return $this->parsed;
    }

    // Holds the current start point of currentString compared to query
    private $cursor;

    private function setCursor($cursor) {
        $this->cursor = $cursor;
        $this->currentString = substr($this->query, $this->cursor);
    }

    // Holds the unparsed contents of query, starting at the cursor
    private $currentString;

    public function __construct($query, $startingToken = self::TOKEN_QUERY, array $options = []) {
        // Always make sure the query has trailing whitespace to ensure that the trailing token can
        // be retrieved
        $this->query = $query.' ';

        $this->parsed = [];

        $this->cursor = 0;
        $this->currentString = $this->query;

        $this->parsed = $this->grabToken($startingToken, false, $options);
    }

    private function throwException($message) {
        $currentStringLength = strlen($this->currentString);

        // Trim current string to tidbit, and make sure trailing space isn't shown
        if ($currentStringLength > self::EXCEPTION_TIDBIT_LENGTH) {
            $actualCurrentString = substr($this->currentString, 0, self::EXCEPTION_TIDBIT_LENGTH).
                ($currentStringLength > self::EXCEPTION_TIDBIT_LENGTH + 1 ? '...' : '');
        } else {
            $actualCurrentString = substr($this->currentString, 0, -1);
        }

        throw new Exception($message.' at "'.$actualCurrentString.'" (Index '.$this->cursor.')');
    }



    /**
     * The grab* methods exist to abstract the process of parsing the next part of the string.
     * Certain methods like grab only consume a set number of characters, whereas grabToken
     * is an interface that will grab a specified token next.
     */

    private function grab($characters) {
        if (!is_int($characters)) { return false; }
        if ($characters <= 0) { return 0; }

        $this->cursor += $characters;
        $this->currentString = substr($this->currentString, $characters);

        return $characters;
    }

    private function grabToken($token, $optional = false, array $options = null) {
        $cursor = $this->cursor;

        $methodName = $token.'Token';
        if (is_null($token) || !method_exists($this, $methodName)) {
            throw new Exception('Token '.$token.' does not exist');
        } else {
            try {
                return call_user_func(array($this, $methodName), $options);
            } catch (Exception $e) {
                if ($optional === true) {
                    $this->setCursor($cursor);
                    return false;
                } else {
                    throw $e;
                }
            }
        }
    }

    private function grabRegex($regex) {
        $regex = '/^('.$regex.')'.self::TERMINATING_REGEX.'/';
        $match = [];

        $exists = preg_match($regex, $this->currentString, $match);

        if ($exists === false || $exists === 0) {
            return false;
        }

        $match = $match[0];

        if ($match !== null) {
            $location = $this->cursor;
            $this->grab(strlen($match));

            return [
                'value' => $match,
                'location' => $location,
            ];
        } else {
            return false;
        }
    }

    private function grabString($string, $optional = false) {
        $length = strlen($string);
        $actual = substr($this->currentString, 0, $length);

        if ($actual === $string) {
            $this->grab($length);
            return true;
        } else {
            if ($optional) {
                return false;
            } else {
                $this->throwException("Expected '".$string."'");
            }
        }
    }

    private function grabWhitespace($atLeast = 0) {
        $index = 0;
        while ($index < strlen($this->currentString) &&
            ($this->currentString[$index] == ' ' ||
            $this->currentString[$index] === "\t" ||
            $this->currentString[$index] === "\n" ||
            $this->currentString[$index] === "\r")) {
            $index++;
        }

        if ($index < $atLeast) {
            $this->throwException("Whitespace expected");
        }

        $characters = $this->grab($index);

        return $characters;
    }

    private function returnWhitespace() {
        $index = $this->cursor;

        while ($index > 0 &&
            ($this->query[$index - 1] == ' ' ||
            $this->query[$index - 1] === "\t" ||
            $this->query[$index - 1] === "\n" ||
            $this->query[$index - 1] === "\r")) {
            $index--;
        }

        $this->setCursor($index);
    }


    /**
     * All methods below this point are for parsing out individual tokens
     */

    private function queryToken() {
        $result = [];

        $this->grabWhitespace();

        $token = $this->grabRegex('[A-Z]+', false);

        $this->grabWhitespace(1);

        $type = $token['value'];

        // Check that the query starts with a valid token
        switch ($type) {
            case self::KEYWORD_SELECT:    // passthrough
            case self::KEYWORD_INSERT:    // passthrough
                $result[$type] = $this->grabToken(self::TOKEN_LOCATION_GRAPH,
                    ['canHaveAggregations' => (strtolower($type) == self::KEYWORD_SELECT)]
                );
                break;
            case self::KEYWORD_UPDATE:    // passthrough
            case self::KEYWORD_DELETE:    // passthrough
            case self::KEYWORD_UNDELETE:  // passthrough
            case self::KEYWORD_DESCRIBE:  // passthrough
            default:
                $this->throwException("Invalid query type");
                break;
        }

        // Expect whitespace after the last token (return any whitespace, and then grab it to make sure it is there)
        $this->returnWhitespace();
        $this->grabWhitespace(1);

        // WHERE clause check
        switch ($type) {
            case self::KEYWORD_SELECT:    // passthrough
            case self::KEYWORD_INSERT:    // passthrough
                // WHERE clause is optional
                if ($this->grabString('WHERE', true)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_WHERE] = $this->grabToken(self::TOKEN_WHERES);
                }
                break;
            case self::KEYWORD_UPDATE:
                // WHERE clause is not optional
                $this->grabString('WHERE');
                $this->grabWhitespace(1);
                $result[self::KEYWORD_WHERE] = $this->grabToken(self::TOKEN_WHERES);
                break;
            case self::KEYWORD_DELETE:    // passthrough
            case self::KEYWORD_UNDELETE:  // passthrough
            default:
                // Whitespace was taken before the WHERE was checked, return this for the next
                // token check as it will require whitespace before it
                $this->returnWhitespace();
                break;
        }

        // At this point, all of the parsing is done
        $this->grabWhitespace();

        if (strlen($this->currentString) > 0) {
            $this->throwException("Syntax error");
        }

        return $result;
    }

    private function locationGraphToken($options) {
        $token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        $this->grabString(':[');
        $this->grabWhitespace();

        $token2 = $this->grabToken(self::TOKEN_LOCATION_GRAPH_I, false, $options);

        $this->grabWhitespace();
        $this->grabString(']');

        // Nest the graph inside of the entity
        $token1[self::TOKEN_LOCATION_GRAPH] = $token2;

        return $token1;
    }

    private function locationGraphIToken($options) {
        $canHaveAggregations = true;
        if (isset($options['canHaveAggregations']) && $options['canHaveAggregations'] === false) {
            $canHaveAggregations = false;
        }

        if ($canHaveAggregations && ($token1 = $this->grabToken(self::TOKEN_LOCATION_AGGREGATION, true))) {
            // <locationagg>
        } else if ($canHaveAggregations && ($token1 = $this->grabToken(self::TOKEN_NAMESPACE_COUNT, true))) {
            // <schemacount>
        } else if ($token1 = $this->grabToken(self::TOKEN_ENTITY_NAME)) {
            // <entityname>:[ <locationgraphi> ]
            if (($token2 = $this->grabString(':[', true)) !== false) {
                $this->grabWhitespace();

                $token3 = $this->grabToken(self::TOKEN_LOCATION_GRAPH_I, false, $options);

                $this->grabWhitespace();
                $this->grabString(']');

                // Nest the graph inside of the entity
                $token1[self::TOKEN_LOCATION_GRAPH_I] = $token3;
            }
        }

        $this->grabWhitespace();

        if ($token4 = $this->grabString(',', true)) {
            // [ "," <locationgraphi> ]
            $this->grabWhitespace();

            $token5 = $this->grabToken(self::TOKEN_LOCATION_GRAPH_I, $options);

            return array_merge([$token1], $token5);
        }

        return [$token1];
    }

    private function locationToken() {
        $token1 = $this->grabToken(self::TOKEN_NAMESPACE);

        $this->grabString(':');

        $token2 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        return [
            'type' => self::TOKEN_LOCATION,
            self::TOKEN_NAMESPACE => $token1,
            self::TOKEN_COLUMN => $token2,
        ];
    }

    private function namespaceToken() {
        $token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        $schemas[] = $token1;

        if ($token2 = $this->grabString('.', true)) {
            // ["."<namespace>]

            $token3 = $this->grabToken(self::TOKEN_NAMESPACE);

            return array_merge($schemas, $token3);
        }

        return $schemas;
    }

    private function locationAggregationToken() {
        $token1 = $this->grabToken(self::TOKEN_AGGREGATION_FUNCTION_NAME);

        $this->grabString('(');
        $this->grabWhitespace();

        $token2 = $this->grabToken(self::TOKEN_LOCATION);

        $this->grabWhitespace();
        $this->grabString(')');

        return [
            'type' => self::TOKEN_LOCATION_AGGREGATION,
            self::TOKEN_AGGREGATION_FUNCTION_NAME => $token1,
            self::TOKEN_LOCATION => $token2,
        ];
    }

    private function namespaceCountToken() {
        $token1 = $this->grabToken(self::TOKEN_COUNT_FUNCTION_NAME);

        $this->grabString('(');
        $this->grabWhitespace();

        $token2 = $this->grabToken(self::TOKEN_NAMESPACE);

        $this->grabWhitespace();
        $this->grabString(')');

        return [
            'type' => self::TOKEN_NAMESPACE_COUNT,
            self::TOKEN_COUNT_FUNCTION_NAME => $token1,
            self::TOKEN_NAMESPACE => $token2,
        ];
    }

    private function aggregationFunctionNameToken() {
        $cursor = $this->cursor;

        $token1 = $this->grabRegex('[A-Z]+');

        switch ($token1['value']) {
            case self::KEYWORD_SUM:
            case self::KEYWORD_AVERAGE:
            case self::KEYWORD_MEAN:
            case self::KEYWORD_MEDIAN:
            case self::KEYWORD_MIN:
            case self::KEYWORD_MAX:
                break;
            default:
                $this->setCursor($cursor);
                $this->throwException('Invalid aggregation function');
        }

        return $token1;
    }

    private function countFunctionNameToken() {
        $cursor = $this->cursor;

        $token1 = $this->grabRegex('[A-Z]+');

        switch ($token1['value']) {
            case self::TOKEN_COUNT:
                break;
            default:
                $this->setCursor($cursor);
                $this->throwException('Invalid count function');
        }

        return $token1;
    }

    private function entityNameToken() {
        $cursor = $this->cursor;

        $hasBackticks = false;

        // Grab opening backtick
        if ($this->currentString[0] === '`') {
            $hasBackticks = true;
            $this->grab(1);
        }


        // Grab actual name
        if (!$token = $this->grabRegex('[0-9a-zA-Z$_]+')) {
            if ($hasBackticks) {
                // Set the cursor back one position so the opening backtick is shown
                $this->setCursor($cursor);
            }

            $this->throwException("Invalid entity name");
        }

        // Check that there is a backtick if needed, and no backtick if not
        if ($this->currentString[0] !== '`' && $hasBackticks) {
            // Set the cursor back one position so the opening backtick is shown
            $this->setCursor($cursor);
            $this->throwException("Missing closing backtick");
        } else if ($this->currentString[0] === '`' && !$hasBackticks) {
            $this->throwException("Unexpected closing backtick");
        } else if ($this->currentString[0] === '`') {
            $this->grab(1);
        }

        return [
            'type' => self::TOKEN_ENTITY_NAME,
            'value' => $token['value'],
            'withBackticks' => '`'.$token['value'].'`',
            'location' => $token['location'] - ($hasBackticks ? 1 : 0),
        ];
    }
}
