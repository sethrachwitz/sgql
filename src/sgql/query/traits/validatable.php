<?php

namespace SGQL;

use SGQL\Lib\Config as Config;

trait Validatable {
    protected function validateClauseQueryType(array $valid) {
        if (!in_array($this->query, $valid)) {
            throw new \Exception("Not a valid clause for this query type");
        }
    }

    protected function validateParts() {
        if ($this->queryType === self::TYPE_SELECT) {
            if (sizeof($this->query[self::PART_COLUMNS]) == 0) {
                throw new \Exception("Missing columns");
            }
        } else if ($this->queryType === self::TYPE_INSERT) {
        	if (sizeof($this->query[self::PART_VALUES]) == 0) {
        		throw new \Exception("Missing values");
	        }
        }
    }

    protected function validateColumns(array $columns) {
        if (sizeof($columns) == 0) {
            throw new \Exception("No namespace specified");
        } else if (sizeof($columns) > 1) {
            throw new \Exception("Only one top level schema can be specified");
        }

        reset($columns);
        $topLevelSchema = key($columns);

        if (!is_string($topLevelSchema)) {
            throw new \Exception("One or more sets of columns is not referenced by a schema name");
        }

        return [$topLevelSchema => $this->_validateColumns($columns[$topLevelSchema], [$topLevelSchema])];
    }

    private function _validateColumns(array $columns, $namespace) {
        $result = [
            'columns' => []
        ];

        // Will throw exception if the namespace doesn't exist, or if a schema doesn't exist
        $this->graph->getNamespace($namespace);
        $schemaName = $namespace[sizeof($namespace) - 1];

        if (sizeof($columns) === 0) {
            throw new \Exception("Nothing is defined for namespace '".implode('.', $namespace)."'");
        }

        foreach ($columns as $alias => $column) {
            if (is_array($column)) { // Associated schema
                if (!is_string($alias)) {
                    throw new \Exception("One or more sets of columns is not referenced by a schema name");
                }

                $result['namespaces'][$alias] = $this->_validateColumns($column, array_merge($namespace, [$alias]));
            } else {
                $actualColumnName = (is_string($alias) ? $alias : $column);
                $actualNamespaceColumnName = implode('.', $namespace).":".$actualColumnName;
                $namespaceColumnName = implode('.', $namespace).":".$column;

                // Make sure this alias / column name hasn't already been used
                if (array_key_exists($actualColumnName, $result['columns']) || in_array($actualColumnName, $result['columns'])) {
                    throw new \Exception("The column name '".$actualNamespaceColumnName."' has already been used");
                }

                if ($actualColumnName == 'associated_id') {
                	throw new \Exception("'associated_id' is a protected column name for SGQL");
				}

                // Check if this is a valid function
				try {
                	$functionParser = new Parser($column, Parser::TOKEN_FUNCTION, ['requireAlias' => false]);
                	$parsedFunction = $functionParser->getParsed();
                	$function = $this->transformFunction($parsedFunction)[0];
				} catch (\Exception $e) {
                	// Not a valid function
					$function = null;
				}

                if (!is_null($function)) {
					// Valid function
					if (is_string($alias)) {
						// Valid alias for the function
						if ($this->validateFunction($function, $namespace, false)) {
							$result['columns'][$alias] = $function;
						} else {
							throw new \Exception("Invalid namespace '".implode('.', $function['namespace'])."' for function '".$column."'");
						}
					} else {
						// Valid function, but not a valid alias
						throw new \Exception("Function '".$column."' must have an alias");
					}
                } else if ($this->validateColumnExists($namespace, $column, false)) {
                    if (is_string($alias)) {
                    	$primaryColumn = $this->graph->getSchema($schemaName)->getPrimaryColumn();
                    	if ($column == $primaryColumn) {
                    		throw new \Exception("The primary column for a schema cannot be aliased");
						}
                        $result['columns'][$alias] = $column;
                    } else {
                        $result['columns'][] = $column;
                    }
                } else {
                	// Not a valid function or column
					throw new \Exception("Column '".$namespaceColumnName."' does not exist");
				}
            }
        }

        if (count($result['columns']) === 0) {
            throw new \Exception("No columns specified for namespace '".implode('.', $namespace)."'");
        }

        return $result;
    }

    protected function validateColumnsFunctionColumns() {
        // This should be run after columns have been assigned, to validate any functions that have columns,
        // as that can't be done until all of the columns/aliases are assigned to the query array
		reset($this->query);
        $topLevelSchema = key($this->query);

        $namespace = [$topLevelSchema];
        $pointer = &$this->query[$topLevelSchema];

        $this->_validateColumnsFunctionColumns($namespace, $pointer);
    }

    private function _validateColumnsFunctionColumns($namespace, $pointer) {
        foreach ($pointer[self::PART_COLUMNS] as $column) {
            if (is_array($column)) { // Is a function
                $validFunctionColumn = $this->validateFunction($column, $namespace);

                if (!$validFunctionColumn) {
                    throw new \Exception("Invalid column '".$column['column']."' for function '".$column['function'].
                        "(".implode('.', $column['namespace']).(isset($column['column']) ? (':'.$column['column']) : '').")'");
                }
            }
        }

        if (isset($pointer['namespaces'])) {
            foreach ($pointer['namespaces'] as $schemaName => $schema) {
                $schemaPointer = &$pointer['namespaces'][$schemaName];
                $this->_validateColumnsFunctionColumns(array_merge($namespace, [$schemaName]), $schemaPointer);
            }
        } else {
            return;
        }
    }

    private function validateFunction(array $function, array $currentNamespace, $checkColumn = true) {
        $fullNamespace = array_merge($currentNamespace, $function['namespace']);

        if (isset($function['namespace'])) {
            try {
                $this->graph->getNamespace($fullNamespace);
            } catch (\Exception $e) {
                return false;
            }

            if ($checkColumn && isset($function['column'])) {
                $exists = $this->validateColumnExists($fullNamespace, $function['column']);
                return $exists;
            }
        }

        return true;
    }

    private function validateColumnExists($namespace, $columnName, $pointer = null) {
        if (!is_array($this->query)) {
            return false;
        }

        // Check if namespace is valid, assign pointer
        if (is_null($pointer)) {
            $this->graph->getNamespace($namespace); // Validate namespace

            // Test if the first schema in the function's namespace
			reset($this->query);
            $topLevelSchema = key($this->query);

            if ($topLevelSchema === $namespace[0]) {
                $pointer = &$this->query[$topLevelSchema];
            } else {
                return false;
            }
        }

        if (sizeof($namespace) === 1) { // Last schema, so start checking columns
            if ($pointer !== false) {
                foreach ($pointer[self::PART_COLUMNS] as $alias => $column) {
                    $actualColumnName = (is_string($alias) ? $alias : $column);
                    if (is_string($alias) && $columnName === $column) { // Column being checked has been aliased to another name
                        return false;
                    } else if ($columnName === $actualColumnName) {
                        return true;
                    }
                }
            }

            return true; // Column might not actually exist, but the db driver will just throw an exception if that is the case
        } else { // Not the last schema, so make recursive call on the next schema
            // Pop of the current schema from the namespace
            array_shift($namespace);
            if (isset($pointer['namespaces']) && isset($pointer['namespaces'][$namespace[0]])) {
                $pointer = &$pointer['namespaces'][$namespace[0]];
                return $this->validateColumnExists($namespace, $columnName, $pointer);
            } else { // The column isn't being returned in the query results, but it might still exist in a schema
                return $this->validateColumnExists($namespace, $columnName, false);
            }
        }
    }

    protected function validateValues(array $values) {
		if (sizeof($values) == 0) {
			throw new \Exception("No namespace specified");
		} else if (sizeof($values) > 1) {
			throw new \Exception("Only one top level schema can be specified");
		}

		reset($values);
		$topLevelSchema = key($values);

		if (!is_string($topLevelSchema)) {
			throw new \Exception("One or more sets of values is not referenced by a schema name");
		}

		return [$topLevelSchema => $this->_validateValues($values[$topLevelSchema], [$topLevelSchema])];
	}

	private function _validateValues(array $rows, $namespace) {
		$result = [];

		// Will throw exception if the namespace doesn't exist, or if a schema doesn't exist
		$this->graph->getNamespace($namespace);

		if (sizeof($rows) === 0) {
			throw new \Exception("Nothing is defined for namespace '".implode('.', $namespace)."'");
		}

		foreach ($rows as $i => $row) {
			if (!is_array($row)) {
				throw new \Exception("Invalid value structure for '".implode('.', $namespace)."'");
			}

			foreach ($row as $columnName => $value) {
				if (is_array($value)) { // Associated schema
					if (!is_string($columnName)) {
						throw new \Exception("One or more sets of values is not referenced by a schema name");
					}

					$result[$i]['namespaces'][$columnName] = $this->_validateValues($value, array_merge($namespace, [$columnName]));
				} else {
					if ($columnName == 'associated_id') {
						throw new \Exception("'associated_id' is a protected column name for SGQL");
					}

					$result[$i][Query::PART_COLUMNS][$columnName] = $value;
				}
			}
		}

		if (count($result) === 0) {
			throw new \Exception("No values specified for namespace '".implode('.', $namespace)."'");
		}

		return $result;
	}
}
