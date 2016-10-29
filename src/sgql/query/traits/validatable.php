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

        // Will throw exception if the namespace doesn't exist
        $this->graph->getNamespace($namespace);

        $schema = $this->graph->getSchema($namespace[sizeof($namespace) - 1]);

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
                if (!$schema->columnExists($column)) {
                    throw new \Exception("Column '".$namespaceColumnName."' does not exist");
                } else if (array_key_exists($actualColumnName, $result['columns']) || in_array($actualColumnName, $result['columns'])) {
                    throw new \Exception("The column name '".$namespaceColumnName."' has already been used");
                }

                if (is_string($alias)) {
                    $result['columns'][$alias] = $column;
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
}
