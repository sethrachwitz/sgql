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
