<?php

namespace SGQL;

trait Transformable {
    protected function transform($input) {
        $this->parser = new Parser($input);
        $parsed = $this->parser->getParsed();

        if (isset($parsed[Parser::KEYWORD_SELECT])) {
            $this->transformSelect($parsed);
        } else if (isset($parsed[Parser::KEYWORD_INSERT])) {
        	$this->transformInsert($parsed);
        }
    }

    private function transformSelect(array $parsed) {
    	$locationGraph = $parsed[Parser::KEYWORD_SELECT];

    	$topLevelSchemaName = $locationGraph['value'];
		$columns = [$topLevelSchemaName => $this->transformLocationGraph($locationGraph[Parser::TOKEN_LOCATION_GRAPH])];

		$this->select($columns);
    }

    private function transformInsert(array $parsed) {
    	$locationGraph = $parsed[Parser::KEYWORD_INSERT];

    	$columns = $this->transformLocationGraph($locationGraph[Parser::TOKEN_LOCATION_GRAPH]);
    	$valueGraphs = $parsed[Parser::KEYWORD_VALUES];

    	$values = $this->transformValueGraphs($columns, $valueGraphs);

    	$this->insert($values);
    }

    protected function transformLocationGraph(array $parsed) {
		foreach ($parsed as $item) {
			if ($item['type'] == Parser::TOKEN_ENTITY_NAME) {
				if (isset($item[Parser::TOKEN_ALIAS])) {
					$columns[$item[Parser::TOKEN_ALIAS]['value']] = $item['value'];
				} else {
					if (isset($item[Parser::TOKEN_LOCATION_GRAPH])) { // Associated schema
						$columns[$item['value']] = $this->transformLocationGraph($item[Parser::TOKEN_LOCATION_GRAPH]);
					} else { // Plain column
						$columns[] = $item['value'];
					}
				}
			} else if (in_array($item['type'], [Parser::TOKEN_LOCATION_AGGREGATION, Parser::TOKEN_NAMESPACE_COUNT])) { // Only other option is that it is is a function
				$result = $this->collapseFunction($item);
				$alias = key($result);

				$columns[$alias] = $result[$alias];
			} else {
				throw new \Exception("Invalid token type for location graph");
			}
		}

		return $columns;
	}

	protected function transformValueGraphs(array $columns, array $parsed) {
    	$result = [];
    	foreach ($parsed as $row => $value) {
    		$schemaName = $value['value'];
    		$result[$schemaName][] = $this->transformValueGraph($columns, $value[Parser::TOKEN_VALUE_GRAPH], [$schemaName]);
    		// If there are multiple $schemaName's, the validator will throw an exception
	    }

	    return $result;
	}

	protected function transformValueGraph(array $columns, array $parsed, array $namespace) {
    	$result = [];

    	if (count($columns) != count($parsed)) {
    		throw new \Exception("Column / value count mismatch in namespace '".implode('.', $namespace)."'");
	    }

		foreach ($parsed as $i => $item) {
			if (isset($item[Parser::TOKEN_VALUE_GRAPH])) {
				$schemaName = $item['value'];
				if (!isset($columns[$schemaName]) || !is_array($columns[$schemaName])) {
					throw new \Exception("Unable to associate schema '".$schemaName."' with a value");
				}
				$column = $columns[$schemaName];
				// Use [0] because we only allow at most one nested value in string queries, for readability
				$result[$schemaName][0] = $this->transformValueGraph($column, $item[Parser::TOKEN_VALUE_GRAPH], array_merge($namespace, [$schemaName]));
			} else if (isset($item['value'])) {
				if (!isset($columns[$i]) || is_array($columns[$i])) {
					throw new \Exception("No column for value '".$item['value']."'");
				}
				$column = $columns[$i];
				$result[$column] = $item['value'];
			}
		}

		return $result;
	}

	protected function collapseFunction(array $parsed) {
    	if ($parsed['type'] === Parser::TOKEN_LOCATION_AGGREGATION) {
    		$alias = $parsed[Parser::TOKEN_ALIAS]['value'];
    		$function = $parsed[Parser::TOKEN_AGGREGATION_FUNCTION_NAME]['value'].'(';
    		foreach ($parsed[Parser::TOKEN_LOCATION][Parser::TOKEN_NAMESPACE] as $schema) {
    			$function .= $schema['withBackticks'].'.';
			}
			$function = substr($function, 0, -1).':'.$parsed[Parser::TOKEN_LOCATION][Parser::TOKEN_ENTITY_NAME]['withBackticks'].')';
    		return [$alias => $function];
		} else if ($parsed['type'] === Parser::TOKEN_NAMESPACE_COUNT) {
			$alias = $parsed[Parser::TOKEN_ALIAS]['value'];
			$function = $parsed[Parser::TOKEN_COUNT_FUNCTION_NAME]['value'].'(';
			foreach ($parsed[Parser::TOKEN_NAMESPACE] as $schema) {
				$function .= $schema['withBackticks'].'.';
			}
			$function = substr($function, 0, -1).')';
			return [$alias => $function];
		}
	}

    protected function transformFunction(array $parsed) {
        if ($parsed['type'] === Parser::TOKEN_LOCATION_AGGREGATION) {
            $result['function'] = $parsed[Parser::TOKEN_AGGREGATION_FUNCTION_NAME]['value'];

            foreach ($parsed[Parser::TOKEN_LOCATION][Parser::TOKEN_NAMESPACE] as $schema) {
                $result['namespace'][] = $schema['value'];
            }

            $result['column'] = $parsed[Parser::TOKEN_LOCATION][Parser::TOKEN_ENTITY_NAME]['value'];
        } else if ($parsed['type'] === Parser::TOKEN_NAMESPACE_COUNT) {
            $result['function'] = $parsed[Parser::TOKEN_COUNT_FUNCTION_NAME]['value'];

            foreach ($parsed[Parser::TOKEN_NAMESPACE] as $schema) {
                $result['namespace'][] = $schema['value'];
            }
        }

        if (isset($parsed[Parser::TOKEN_ALIAS])) {
            return [
                $parsed[Parser::TOKEN_ALIAS]['value'] => $result
            ];
        } else {
            return [$result]; // Returned as array to follow the same format as returning a function with an alias
        }
    }
}
