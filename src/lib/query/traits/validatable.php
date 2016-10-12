<?php

namespace SGQL\Lib\Query;

trait Validatable {
    protected function validateClauseQueryType(array $valid) {
        if (!in_array($this->queryType, $valid)) {
            throw new \Exception("Not a valid clause for this query type");
        }
    }

    protected function validateParts() {
        if ($this->queryType === self::TYPE_SELECT) {
            if (sizeof($this->parts[self::PART_COLUMNS]) == 0) {
                throw new \Exception("Missing columns");
            } else if ($this->parts[self::PART_FROM] === '') {
                throw new \Exception("Missing FROM clause");
            }
        }
    }

    private function validateColumns($columns) {
        foreach ($columns as $tableName => $tableColumns) {
            if (is_array($tableColumns)) {
                if (sizeof($tableColumns) == 0) {
                    throw new \Exception("No columns listed for '".$tableName."'");
                } else {
                    foreach ($tableColumns as $column) {
                        if (!is_string($column)) {
                            throw new \Exception("All columns must be strings");
                        }
                    }
                }
            } else if ($tableColumns !== '*') {
                throw new \Exception("Invalid column structure");
            }
        }

        return $columns;
    }

    private function validateFrom($table) {
        $this->validateClauseQueryType([self::TYPE_SELECT]);

        if (
            !(is_string($table) || $table !== '') &&
            !($table instanceof Query)
        ) {
            throw new \Exception("Invalid table");
        }

        return $table;
    }

    private function validateJoin($table, $conditions, $type) {
        $this->validateClauseQueryType([self::TYPE_SELECT]);
        if ( // Test that all of these options are not true
            !(is_string($table)) && // Join table with no alias
            !(is_array($table) && sizeof($table) == 1 && is_string($table[0])) && // Join table with alias
            !($table instanceof Query) // Join table that is a subquery
        ) {
            throw new \Exception("Joined table is invalid");
        }

        if (!in_array($type, self::JOIN_TYPES)) {
            throw new \Exception("Invalid join type '".$type."'");
        }

        return [
            'table' => $table,
            'conditions' => $conditions,
            'type' => $type,
        ];
    }

    private function validateWhere($condition) {
        $this->validateClauseQueryType([self::TYPE_SELECT]);

        return $condition;
    }
}
