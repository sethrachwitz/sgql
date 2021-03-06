<?php

namespace SGQL;

class Parser {
    // Keywords
    const TOKEN_QUERY = 'query';
    const KEYWORD_SELECT = 'SELECT';
    const KEYWORD_INSERT = 'INSERT';
    const KEYWORD_UPDATE = 'UPDATE';
    const KEYWORD_DELETE = 'DELETE';
    const KEYWORD_DESCRIBE = 'DESCRIBE';
    const KEYWORD_CREATE = 'CREATE';
    const KEYWORD_DESTROY = 'DESTROY';
	const KEYWORD_SHOW = 'SHOW'; // Multipurpose

    const KEYWORD_ASSOCIATION = 'ASSOCIATION';
    const TOKEN_ASSOC = 'assoc';
    const TOKEN_ASSOC_TYPE = 'assocType';

    const KEYWORD_WHERE = 'WHERE';
    const TOKEN_WHERES = 'wheres';
    const TOKEN_WHERE_COMPARE = 'whereCompare';
    const TOKEN_COMPARES = 'compares';
    const TOKEN_COMPARE = 'compare';

    const KEYWORD_VALUES = 'VALUES';
    const TOKEN_VALUES = 'values';

    const KEYWORD_SET = 'SET';
    const TOKEN_SETS = 'sets';
    const TOKEN_ENTITY_ASSIGN = 'entityAssign';

    const KEYWORD_ASSOCIATE = 'ASSOCIATE';
    const KEYWORD_DISASSOCIATE = 'DISASSOCIATE';
    const TOKEN_ASSOCIATES = 'associates';
    const TOKEN_COLUMN_COMPARE = 'columnCompare';

    const KEYWORD_ORDER = 'ORDER';
    const TOKEN_ORDERS = 'orders';
    const TOKEN_ORDER_BY = 'orderBy';
    const TOKEN_ORDER_DIRECTION = 'orderDirection';

    const KEYWORD_PAGE = 'PAGE';
    const TOKEN_SHOWS = 'shows';
    const TOKEN_SHOW_I = 'showI';

    // Graphs
    const TOKEN_LOCATION_GRAPH = 'locationGraph';
    const TOKEN_LOCATION_GRAPH_I = 'locationGraphI';

    const TOKEN_VALUE_GRAPHS = 'valueGraphs';
    const TOKEN_VALUE_GRAPH = 'valueGraph';
    const TOKEN_VALUE_GRAPH_I = 'valueGraphI';

    // Functions and their inputs
    const TOKEN_FUNCTION = 'function';
    const TOKEN_LOCATION_AGGREGATION = 'locationAggregation';
    const TOKEN_NAMESPACE_COUNT = 'namespaceCount';
    const TOKEN_ALIAS = 'alias';

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

    const TOKEN_HAS_COMPARE = 'hasCompare';

    const TOKEN_COMPARISON = 'comparison';
    const TOKEN_ASSIGNMENT = 'assignment';

    const TOKEN_VALUE = 'value';
    const TOKEN_PARAMETER = 'parameter';

    const TOKEN_POSITIVE_INTEGER = 'positiveInteger';
    const TOKEN_INTEGER = 'integer';
    const TOKEN_DOUBLE = 'double';
    const TOKEN_STRING = 'string';
    const TOKEN_BOOLEAN = 'boolean';

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

        throw new \Exception($message.' at "'.$actualCurrentString.'" (Index '.$this->cursor.')');
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
            throw new \Exception('Token '.$token.' does not exist');
        } else {
            try {
                return call_user_func(array($this, $methodName), $options);
            } catch (\Exception $e) {
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

        $type = $token['value'];

        // Check that the query starts with a valid token
        switch ($type) {
            case self::KEYWORD_SELECT:    // passthrough
            case self::KEYWORD_INSERT:
                $this->grabWhitespace(1);
                $result[$type] = $this->grabToken(self::TOKEN_LOCATION_GRAPH,
                    ['canHaveAggregations' => ($type == self::KEYWORD_SELECT)]
                );
                break;
            case self::KEYWORD_UPDATE:    // passthrough
            case self::KEYWORD_DELETE:
                $this->grabWhitespace(1);
                $result[$type] = $this->grabToken(self::TOKEN_ENTITY_NAME);
                break;
            case self::KEYWORD_DESCRIBE:
                $this->grabWhitespace(1);
                $result[$type] = $this->grabToken(self::TOKEN_ENTITY_NAME);
                break;
	        case self::KEYWORD_CREATE:    // passthrough
            case self::KEYWORD_DESTROY:
	        	$this->grabWhitespace(1);
	        	$subtype = $this->grabRegex('[A-Z]+');
	        	if ($subtype) {
	        		$subtype = $subtype['value'];
		        }
	        	if ($subtype == self::KEYWORD_ASSOCIATION) {
			        $this->grabWhitespace(1);
	        		$result[$type][$subtype] = $this->grabToken(self::TOKEN_ASSOC);
		        } else {
	        		echo $subtype;
		        }
		        break;
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
            case self::KEYWORD_INSERT:
                // WHERE clause is optional
                if ($this->grabString(self::KEYWORD_WHERE, true)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_WHERE] = $this->grabToken(self::TOKEN_WHERES);
                }
                break;
            case self::KEYWORD_UPDATE:    // passthrough
            case self::KEYWORD_DELETE:
                // WHERE clause is not optional
                $this->grabString(self::KEYWORD_WHERE);
                $this->grabWhitespace(1);
                $result[self::KEYWORD_WHERE] = $this->grabToken(self::TOKEN_WHERES);
                break;
            default:
                $cursor = $this->cursor;
                if ($this->grabString(self::KEYWORD_WHERE, true)) {
                    $this->setCursor($cursor);
                    $this->throwException("Syntax error");
                }
                // Whitespace was taken before the WHERE was checked, return this for the next
                // token check as it will require whitespace before it
                $this->returnWhitespace();
                break;
        }

        // Expect whitespace after the last token (return any whitespace, and then grab it to make sure it is there)
        $this->returnWhitespace();
        $this->grabWhitespace(1);

        // VALUES clause check
        switch ($type) {
            case self::KEYWORD_INSERT:
                // VALUES clause is not optional
                if ($this->grabString(self::KEYWORD_VALUES)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_VALUES] = $this->grabToken(self::TOKEN_VALUE_GRAPHS);
                }
                break;
            case self::KEYWORD_SELECT:    // passthrough
            case self::KEYWORD_UPDATE:    // passthrough
            case self::KEYWORD_DELETE:
            default:
                $cursor = $this->cursor;
                if ($this->grabString(self::KEYWORD_VALUES, true)) {
                    $this->setCursor($cursor);
                    $this->throwException("Syntax error");
                }
                // Whitespace was taken before the VALUES was checked, return this for the next
                // token check as it will require whitespace before it
                $this->returnWhitespace();
                break;
        }

        // Expect whitespace after the last token (return any whitespace, and then grab it to make sure it is there)
        $this->returnWhitespace();
        $this->grabWhitespace(1);

        // SET clause check
        switch ($type) {
            case self::KEYWORD_UPDATE:
                // SET clause is optional
                if ($this->grabString(self::KEYWORD_SET, true)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_SET] = $this->grabToken(self::TOKEN_SETS);
                }
                break;
            case self::KEYWORD_SELECT:    // passthrough
            case self::KEYWORD_INSERT:    // passthrough
            case self::KEYWORD_DELETE:
            default:
                $cursor = $this->cursor;
                if ($this->grabString(self::KEYWORD_SET, true)) {
                    $this->setCursor($cursor);
                    $this->throwException("Syntax error");
                }
                // Whitespace was taken before the SET was checked, return this for the next
                // token check as it will require whitespace before it
                $this->returnWhitespace();
                break;
        }

        // Expect whitespace after the last token (return any whitespace, and then grab it to make sure it is there)
        $this->returnWhitespace();
        $this->grabWhitespace(1);

        // ASSOCIATE clause check
        switch ($type) {
            case self::KEYWORD_INSERT:    // passthrough
            case self::KEYWORD_UPDATE:
                // ASSOCIATE clause is optional
                if ($this->grabString(self::KEYWORD_ASSOCIATE, true)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_ASSOCIATE] = $this->grabToken(self::TOKEN_ASSOCIATES);
                }
                break;
            case self::KEYWORD_SELECT:    // passthrough
            case self::KEYWORD_DELETE:
            default:
                $cursor = $this->cursor;
                if ($this->grabString(self::KEYWORD_ASSOCIATE, true)) {
                    $this->setCursor($cursor);
                    $this->throwException("Syntax error");
                }
                // Whitespace was taken before the ASSOCIATE was checked, return this for the next
                // token check as it will require whitespace before it
                $this->returnWhitespace();
                break;
        }

        // Expect whitespace after the last token (return any whitespace, and then grab it to make sure it is there)
        $this->returnWhitespace();
        $this->grabWhitespace(1);

        // DISASSOCIATE clause check
        switch ($type) {
            case self::KEYWORD_UPDATE:
                // DISASSOCIATE clause may be optional
                $optional = (isset($result[self::KEYWORD_SET]) || isset($result[self::KEYWORD_ASSOCIATE]));
                if ($this->grabString(self::KEYWORD_DISASSOCIATE, $optional)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_DISASSOCIATE] = $this->grabToken(self::TOKEN_ASSOCIATES);
                }
                break;
            case self::KEYWORD_SELECT:    // passthrough
            case self::KEYWORD_INSERT:    // passthrough
            case self::KEYWORD_DELETE:
            default:
                $cursor = $this->cursor;
                if ($this->grabString(self::KEYWORD_DISASSOCIATE, true)) {
                    $this->setCursor($cursor);
                    $this->throwException("Syntax error");
                }
                // Whitespace was taken before the DISASSOCIATE was checked, return this for the next
                // token check as it will require whitespace before it
                $this->returnWhitespace();
                break;
        }

        // Expect whitespace after the last token (return any whitespace, and then grab it to make sure it is there)
        $this->returnWhitespace();
        $this->grabWhitespace(1);

        // ORDER clause check
        switch ($type) {
            case self::KEYWORD_SELECT:
                // ORDER clause is optional
                if ($this->grabString(self::KEYWORD_ORDER, true)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_ORDER] = $this->grabToken(self::TOKEN_ORDERS);
                }
                break;
            case self::KEYWORD_UPDATE:    // passthrough
            case self::KEYWORD_INSERT:    // passthrough
            case self::KEYWORD_DELETE:
            default:
                $cursor = $this->cursor;
                if ($this->grabString(self::KEYWORD_ORDER, true)) {
                    $this->setCursor($cursor);
                    $this->throwException("Syntax error");
                }
                // Whitespace was taken before the ORDER was checked, return this for the next
                // token check as it will require whitespace before it
                $this->returnWhitespace();
                break;
        }

        // Expect whitespace after the last token (return any whitespace, and then grab it to make sure it is there)
        $this->returnWhitespace();
        $this->grabWhitespace(1);

        // SHOW clause check
        switch ($type) {
            case self::KEYWORD_SELECT:
                // ORDER clause is optional
                if ($this->grabString(self::KEYWORD_SHOW, true)) {
                    $this->grabWhitespace(1);
                    $result[self::KEYWORD_SHOW] = $this->grabToken(self::TOKEN_SHOWS);
                }
                break;
            case self::KEYWORD_UPDATE:    // passthrough
            case self::KEYWORD_INSERT:    // passthrough
            case self::KEYWORD_DELETE:
            default:
                $cursor = $this->cursor;
                if ($this->grabString(self::KEYWORD_SHOW, true)) {
                    $this->setCursor($cursor);
                    $this->throwException("Syntax error");
                }
                // Whitespace was taken before the SHOW was checked, return this for the next
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

    private function assocToken() {
		$token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);

		$this->grabWhitespace(1);

		$token2 = $this->grabToken(self::TOKEN_ASSOC_TYPE);

		$this->grabWhitespace(1);

		$token3 = $this->grabToken(self::TOKEN_ENTITY_NAME);

		return [
			'parent' => $token1,
			self::TOKEN_ASSOC_TYPE => $token2,
			'child' => $token3,
		];
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

        if ($canHaveAggregations && ($token1 = $this->grabToken(self::TOKEN_LOCATION_AGGREGATION, true, ['requireAlias' => true]))) {
            // <locationagg>
        } else if ($canHaveAggregations && ($token1 = $this->grabToken(self::TOKEN_NAMESPACE_COUNT, true, ['requireAlias' => true]))) {
            // <schemacount>
        } else if ($token1 = $this->grabToken(self::TOKEN_ENTITY_NAME)) {
            if (($token2 = $this->grabString(':[', true)) !== false) {
                // <entityname>:[ <locationgraphi> ]
                $this->grabWhitespace();

                $token3 = $this->grabToken(self::TOKEN_LOCATION_GRAPH_I, false, $options);

                $this->grabWhitespace();
                $this->grabString(']');

                // Nest the graph inside of the entity
                $token1[self::TOKEN_LOCATION_GRAPH] = $token3;
            } else {
                // <entityname> [<alias>]
                try {
                    $this->grabWhitespace(1);
                    $token2 = $this->grabToken(self::TOKEN_ALIAS);
                    $token1[self::TOKEN_ALIAS] = $token2;
                } catch (\Exception $e) {
                    $this->returnWhitespace();
                }
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

    private function valueGraphsToken() {
	    $token1 = $this->grabToken(self::TOKEN_VALUE_GRAPH);

	    try {
		    $this->grabWhitespace(0);
		    $this->grabString(",");
	    } catch (\Exception $e) {
		    // No other associates to see here
		    $this->returnWhitespace();
		    return [$token1];
	    }

	    $this->grabWhitespace();

	    $token2 = $this->grabToken(self::TOKEN_VALUE_GRAPHS);

	    return array_merge([$token1], $token2);
    }

    private function valueGraphToken() {
        $token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        $this->grabString(':[');
        $this->grabWhitespace();

        $token2 = $this->grabToken(self::TOKEN_VALUE_GRAPH_I);

        $this->grabWhitespace();
        $this->grabString(']');

        // Nest the graph inside of the entity
        $token1[self::TOKEN_VALUE_GRAPH] = $token2;

        return $token1;
    }

    private function valueGraphIToken() {
        // Check if value or entity (which would then have a nested graph)
        if ($token1 = $this->grabToken(self::TOKEN_VALUE, true)) {
            $this->grabWhitespace();
        } else if ($token1 = $this->grabToken(self::TOKEN_ENTITY_NAME, true)) { // Token is an entity, so it will have a nested graph
            $token2 = $this->grabString(':[');
            $this->grabWhitespace();

            $token3 = $this->grabToken(self::TOKEN_VALUE_GRAPH_I);

            $this->grabWhitespace();
            $this->grabString(']');

            // Nest the graph inside of the entity
            $token1[self::TOKEN_VALUE_GRAPH] = $token3;
        } else { // Neither valid token was found
            $this->throwException("Invalid entity name or value");
        }

        // Check for more values and merge them into this graph if they exist
	    if ($token4 = $this->grabString(',', true)) {
		    // [ "," <locationgraphi> ]
		    $this->grabWhitespace();

		    $token5 = $this->grabToken(self::TOKEN_VALUE_GRAPH_I);

		    return array_merge([$token1], $token5);
	    }


        return [$token1];
    }

    private function wheresToken() {
        $token1 = $this->grabToken(self::TOKEN_WHERE_COMPARE);

        try {
            $this->grabWhitespace();
            $this->grabString("AND");
        } catch (\Exception $e) {
            // No other comparisons to see here
            $this->returnWhitespace();
            return [$token1];
        }

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_WHERES);

        return array_merge([$token1], $token2);
    }

    private function whereCompareToken() {
        $token1 = $this->grabToken(self::TOKEN_NAMESPACE);

        $this->grabString(":(");
        $this->grabWhitespace();

        $token2 = $this->grabToken(self::TOKEN_COMPARES);

        $this->grabWhitespace();
        $this->grabString(")");

        return [
            'type' => self::TOKEN_WHERE_COMPARE,
            self::TOKEN_NAMESPACE => $token1,
            self::TOKEN_COMPARES => $token2
        ];
    }

    private function comparesToken() {
        $token1 = $this->grabToken(self::TOKEN_COMPARE);

        try {
            $this->grabWhitespace(1);
            $this->grabString("AND");
        } catch (\Exception $e) {
            // No other comparisons to see here
            $this->returnWhitespace();
            return [$token1];
        }

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_COMPARES);

        return array_merge([$token1], $token2);
    }

    private function compareToken() {
        // Find out if this is a <entityname> or <colagg> token
        if (!$token1 = $this->grabToken(self::TOKEN_LOCATION_AGGREGATION, true)) {
            if (!$token1 = $this->grabToken(self::TOKEN_NAMESPACE_COUNT, true)) {
                if (!$token1 = $this->grabToken(self::TOKEN_HAS_COMPARE, true)) {
                    try {
                        $token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);
                    } catch (\Exception $e) {
                        $this->throwException("Invalid location aggregation or entity name");
                    }
                }
            }
        }

        $this->grabWhitespace(1);

        if ($this->grabString("IN", true)) {
            if ($token1['type'] != self::TOKEN_ENTITY_NAME) {
                $this->setCursor($this->cursor - 2);
                $this->throwException("Use of 'IN' is limited to non-aggregation comparisons");
            }

            $token2 = [
                'type' => self::TOKEN_COMPARISON,
                'value' => 'IN',
                'location' => $this->cursor - 2, // Length of "IN"
            ];

            $this->grabWhitespace(1);

            $token3 = $this->grabToken(self::TOKEN_PARAMETER);
        } else {
            $token2 = $this->grabToken(self::TOKEN_COMPARISON);

            $this->grabWhitespace(1);

            // Grab token or value
            if (!$token3 = $this->grabToken(self::TOKEN_VALUE, true)) {
                try {
                    $token3 = $this->grabToken(self::TOKEN_PARAMETER);
                } catch (\Exception $e) {
                    $this->throwException("Invalid value or parameter");
                }
            }
        }

        return [
            'type' => self::TOKEN_COMPARE,
            'key' => $token1,
            self::TOKEN_COMPARISON => $token2,
            'value' => $token3,
        ];
    }

    private function setsToken() {
        $token1 = $this->grabToken(self::TOKEN_ENTITY_ASSIGN);

        try {
            $this->grabWhitespace(0);
            $this->grabString(",");
        } catch (\Exception $e) {
            // No other comparisons to see here
            $this->returnWhitespace();
            return [$token1];
        }

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_SETS);

        return array_merge([$token1], $token2);
    }

    private function entityAssignToken() {
        $token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        $this->grabWhitespace(1);

        $this->grabToken(self::TOKEN_ASSIGNMENT);

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_VALUE);

        return [
            'type' => self::TOKEN_ENTITY_ASSIGN,
            'key' => $token1,
            'value' => $token2,
        ];
    }

    private function associatesToken() {
        $token1 = $this->grabToken(self::TOKEN_COLUMN_COMPARE);

        try {
            $this->grabWhitespace(0);
            $this->grabString(",");
        } catch (\Exception $e) {
            // No other associates to see here
            $this->returnWhitespace();
            return [$token1];
        }

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_ASSOCIATES);

        return array_merge([$token1], $token2);
    }

    private function ordersToken() {
        $token1 = $this->grabToken(self::TOKEN_ORDER_BY);

        try {
            $this->grabWhitespace();
            $this->grabString(",");
        } catch (\Exception $e) {
            // No other comparisons to see here
            $this->returnWhitespace();
            return [$token1];
        }

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_ORDERS);

        return array_merge([$token1], $token2);
    }

    private function orderByToken() {
        $token1 = $this->grabToken(self::TOKEN_NAMESPACE);

        $this->grabWhitespace(1);

        $this->grabString("BY");

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        $this->grabWhitespace(1);

        $token3 = $this->grabToken(self::TOKEN_ORDER_DIRECTION);

        return [
            'type' => self::TOKEN_ORDER_BY,
            self::TOKEN_NAMESPACE => $token1,
            self::TOKEN_ENTITY_NAME => $token2,
            self::TOKEN_ORDER_DIRECTION => $token3,
        ];
    }

    private function orderDirectionToken() {
        $cursor = $this->cursor;

        $token1 = $this->grabRegex('[A-Z]+', false);

        if ($token1['value'] != 'ASC' && $token1['value'] != 'DESC') {
            $this->setCursor($cursor);
            $this->throwException("Invalid order direction");
        }

        return [
            'type' => self::TOKEN_ORDER_DIRECTION,
            'value' => $token1['value'],
            'location' => $token1['location'],
        ];
    }

    private function showsToken() {
        $token1 = $this->grabToken(self::TOKEN_SHOW_I);

        try {
            $this->grabWhitespace();
            $this->grabString(",");
        } catch (\Exception $e) {
            // No other comparisons to see here
            $this->returnWhitespace();
            return [$token1];
        }

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_SHOWS);

        return array_merge([$token1], $token2);
    }

    private function showIToken() {
        $token1 = $this->grabToken(self::TOKEN_POSITIVE_INTEGER);

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_NAMESPACE);

        if (sizeof($token2) > 1) {
            return [
                'type' => self::TOKEN_SHOW_I,
                'records' => $token1,
                self::TOKEN_NAMESPACE => $token2,
            ];
        }

        // Namespace is only one schema
        $token2 = $token2[0];

        $cursor = $this->cursor;

        // Check to see if there could be a PAGE clause
        try {
            $this->grabWhitespace(1);
        } catch (\Exception $e) {
            // No whitespace, so there can't possibly be a PAGE clause
            return [
                'type' => self::TOKEN_SHOW_I,
                'records' => $token1,
                self::TOKEN_SCHEMA => $token2,
            ];
        }

        if ($this->grabString(self::KEYWORD_PAGE, true)) {
            $this->grabWhitespace(1);

            $token3 = $this->grabToken(self::TOKEN_POSITIVE_INTEGER);

            return [
                'type' => self::TOKEN_SHOW_I,
                'records' => $token1,
                self::TOKEN_SCHEMA => $token2,
                'page' => $token3,
            ];
        } else {
            $this->setCursor($cursor);

            return [
                'type' => self::TOKEN_SHOW_I,
                'records' => $token1,
                self::TOKEN_SCHEMA => $token2,
            ];
        }
    }

    private function columnCompareToken() {
        $token1 = $this->grabToken(self::TOKEN_COLUMN);

        $this->grabWhitespace(1);

        $token2 = $this->grabToken(self::TOKEN_COMPARISON);

        $this->grabWhitespace(1);

        $token3 = $this->grabToken(self::TOKEN_VALUE);

        return [
            'type' => self::TOKEN_COLUMN_COMPARE,
            self::TOKEN_COLUMN => $token1,
            self::TOKEN_COMPARISON => $token2,
            self::TOKEN_VALUE => $token3,
        ];
    }

    private function locationToken() {
        $token1 = $this->grabToken(self::TOKEN_NAMESPACE);

        $this->grabString(':');

        $token2 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        return [
            'type' => self::TOKEN_LOCATION,
            self::TOKEN_NAMESPACE => $token1,
            self::TOKEN_ENTITY_NAME => $token2,
        ];
    }

    private function columnToken() {
        $token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        $this->grabString(':');

        $token2 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        return [
            'type' => self::TOKEN_COLUMN,
            self::TOKEN_SCHEMA => $token1,
            self::TOKEN_ENTITY_NAME => $token2,
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

    private function functionToken($options) {
        $requireAlias = false;
        if (isset($options['requireAlias']) && $options['requireAlias'] === true) {
            $requireAlias = true;
        }

        if ($token1 = $this->grabToken(self::TOKEN_NAMESPACE_COUNT, true, ['requireAlias' => $requireAlias])) {
            return $token1;
        } else if ($token1 = $this->grabToken(self::TOKEN_LOCATION_AGGREGATION, true, ['requireAlias' => $requireAlias])) {
            return $token1;
        } else {
            throw new \Exception("Invalid function");
        }
    }

    private function locationAggregationToken($options) {
        $requireAlias = false;
        if (isset($options['requireAlias']) && $options['requireAlias'] === true) {
            $requireAlias = true;
        }

        $token1 = $this->grabToken(self::TOKEN_AGGREGATION_FUNCTION_NAME);

        $this->grabString('(');
        $this->grabWhitespace();

        $token2 = $this->grabToken(self::TOKEN_LOCATION);

        $this->grabWhitespace();
        $this->grabString(')');

        $result = [
            'type' => self::TOKEN_LOCATION_AGGREGATION,
            self::TOKEN_AGGREGATION_FUNCTION_NAME => $token1,
            self::TOKEN_LOCATION => $token2,
        ];

        if ($requireAlias) {
            $this->grabWhitespace(1);
            $token3 = $this->grabToken(self::TOKEN_ALIAS);

            $result[self::TOKEN_ALIAS] = $token3;
        }

        return $result;
    }

    private function namespaceCountToken($options) {
        $requireAlias = false;
        if (isset($options['requireAlias']) && $options['requireAlias'] === true) {
            $requireAlias = true;
        }

        $token1 = $this->grabToken(self::TOKEN_COUNT_FUNCTION_NAME);

        $this->grabString('(');
        $this->grabWhitespace();

        $token2 = $this->grabToken(self::TOKEN_NAMESPACE);

        $this->grabWhitespace();
        $this->grabString(')');

        $result = [
            'type' => self::TOKEN_NAMESPACE_COUNT,
            self::TOKEN_COUNT_FUNCTION_NAME => $token1,
            self::TOKEN_NAMESPACE => $token2,
        ];

        if ($requireAlias) {
            $this->grabWhitespace(1);
            $token3 = $this->grabToken(self::TOKEN_ALIAS);

            $result[self::TOKEN_ALIAS] = $token3;
        }

        return $result;
    }

    private function hasCompareToken() {
        $this->grabString("HAS(");

        $this->grabWhitespace();

        $token1 = $this->grabToken(self::TOKEN_LOCATION);

        $this->grabWhitespace(1);

        if ($this->grabString("IN", true)) { // Uses "IN" instead of comparison operator
            $token2 = [
                'type' => self::TOKEN_COMPARISON,
                'value' => 'IN',
                'location' => $this->cursor - 2, // Length of "IN"
            ];

            $this->grabWhitespace(1);

            $token3 = $this->grabToken(self::TOKEN_PARAMETER);
        } else { // Should be a comparison operator
            $token2 = $this->grabToken(self::TOKEN_COMPARISON);

            $this->grabWhitespace(1);

            $token3 = $this->grabToken(self::TOKEN_VALUE);
        }

        $this->grabWhitespace();

        $this->grabString(")");

        return [
            'type' => self::TOKEN_HAS_COMPARE,
            self::TOKEN_LOCATION => $token1,
            self::TOKEN_COMPARISON => $token2,
            self::TOKEN_VALUE => $token3,
        ];
    }

    private function aliasToken() {
        $cursor = $this->cursor;
        $this->grabString("AS");
        $this->grabWhitespace(1);

        $token1 = $this->grabToken(self::TOKEN_ENTITY_NAME);

        return [
            'type' => self::TOKEN_ALIAS,
            'value' => $token1['value'],
            'withBackticks' => $token1['withBackticks'],
            'location' => $cursor
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

    private function assocTypeToken() {
	    if (!$token1 = $this->grabRegex('(-|<-|<->)')) {
		    $this->throwException("Expected association type");
	    }

	    return [
		    'type' => self::TOKEN_ASSOC_TYPE,
		    'value' => $token1['value'],
		    'location' => $token1['location'],
	    ];
    }

    private function comparisonToken() {
        if (!$token1 = $this->grabRegex('(==|!=|<|>|<=|>=)')) {
            $this->throwException("Expected comparison operator");
        }

        return [
            'type' => self::TOKEN_COMPARISON,
            'value' => $token1['value'],
            'location' => $token1['location'],
        ];
    }

    private function assignmentToken() {
        if (!$token1 = $this->grabRegex('(=)')) {
            $this->throwException("Expected assignment operator");
        }

        return [
            'type' => self::TOKEN_ASSIGNMENT,
            'value' => $token1['value'],
            'location' => $token1['location'],
        ];
    }

    private function valueToken() {
        if (!$token1 = $this->grabToken(self::TOKEN_DOUBLE, true)) {
            if (!$token1 = $this->grabToken(self::TOKEN_INTEGER, true)) {
                if (!$token1 = $this->grabToken(self::TOKEN_STRING, true)) {
                    if (!$token1 = $this->grabToken(self::TOKEN_BOOLEAN, true)) {
                        if (!$token1 = $this->grabToken(self::TOKEN_PARAMETER, true)) {
                            $this->throwException("Invalid value");
                        }
                    }
                }
            }
        }

        return $token1;
    }

    private function positiveIntegerToken() {
        $cursor = $this->cursor;
        $token1 = $this->grabToken(self::TOKEN_INTEGER);

        $value = (int)$token1['value'];

        if ($value <= 0) {
            $this->setCursor($cursor);
            $this->throwException("Integer must be positive");
        }

        $token1['type'] = self::TOKEN_POSITIVE_INTEGER;
        return $token1;
    }

    private function integerToken() {
        if (!$token1 = $this->grabRegex('[-]?[0-9]+')) {
            $this->throwException('Invalid integer');
        }

        return [
            'type' => self::TOKEN_INTEGER,
            'value' => $token1['value'],
            'location' => $token1['location'],
        ];
    }

    private function doubleToken() {
        if (!$token1 = $this->grabRegex('[-]?[0-9]*\.[0-9]+')) {
            $this->throwException('Invalid double');
        }

        return [
            'type' => self::TOKEN_DOUBLE,
            'value' => $token1['value'],
            'location' => $token1['location'],
        ];
    }

    private function stringToken() {
        if (!$token1 = $this->grabRegex('L?\"(\\.|[^\\"])*\"')) {
            $this->throwException('Invalid string');
        }

        return [
            'type' => self::TOKEN_STRING,
            'value' => substr($token1['value'], 1, -1),
            'withQuotes' => $token1['value'],
            'location' => $token1['location'],
        ];
    }

    private function booleanToken() {
        if (!$token1 = $this->grabRegex('true|false')) {
            $this->throwException('Invalid boolean');
        }

        return [
            'type' => self::TOKEN_BOOLEAN,
            'value' => $token1['value'],
            'location' => $token1['location'],
        ];
    }

    private function parameterToken() {
        if (!$token1 = $this->grabRegex('\?[0-9a-zA-Z]+')) {
            $this->throwException("Invalid parameter name");
        }

        // Trim off the ?
        $token1['value'] = substr($token1['value'], 1);

        return [
            'type' => self::TOKEN_PARAMETER,
            'value' => $token1['value'],
            'location' => $token1['location'],
        ];
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
