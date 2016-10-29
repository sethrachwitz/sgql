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
        }
    }

    protected function validateColumns(array $columns) {
        if (sizeof($columns) == 0) {
            throw new \Exception("No namespace specified");
        } else if (sizeof($columns) > 2) {
            throw new \Exception("Only one top level namespace can be specified");
        }

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

        if (sizeof($columns) === 0) {
            throw new \Exception("Nothing is defined for namespace '".implode('.', $namespace)."'");
        }

        foreach ($columns as $alias => $column) {
            if (is_array($column)) { // Column name / function
                if (!is_string($alias)) {
                    throw new \Exception("One or more sets of columns is not referenced by a schema name");
                }

                $result['namespaces'][$alias] = $this->_validateColumns($column, array_merge($namespace, [$alias]));
            } else {
                $actualColumnName = (is_string($alias) ? $alias : $column);
                $namespaceColumnName = implode('.', $namespace).":".$actualColumnName;

                // Make sure the column is valid
                    throw new \Exception("Column '".$namespaceColumnName."' does not exist");
                } else if (array_key_exists($actualColumnName, $result['columns']) || in_array($actualColumnName, $result['columns'])) {
                    throw new \Exception("The column name '".$namespaceColumnName."' has already been used");
                }

                if (is_string($alias)) {
                    $result['columns'][$alias] = $column;
                if (!$this->validateColumnExists($namespace, $column, false)) {
                } else {
                    $result['columns'][] = $column;
                }
            }
        }

        if (count($result['columns']) === 0) {
            throw new \Exception("No columns specified for namespace '".implode('.', $namespace)."'");
        }

        return $result;
    }

    private function validateColumnExists($namespace, $columnName, $pointer = null) {
        if (!is_array($this->query)) {
            return false;
        }

        // Check if namespace is valid, assign pointer
        if (is_null($pointer)) {
            $this->graph->getNamespace($namespace); // Validate namespace

            // Test if the first schema in the function's namespace
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

            // If we got to this point, the column isn't going to be returned in the query, but it might still exist in a schema
            return $this->graph->getSchema($namespace[0])->columnExists($columnName);
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
}
