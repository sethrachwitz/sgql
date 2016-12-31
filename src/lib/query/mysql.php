<?php

namespace SGQL\Lib\Query;

include_once(dirname(__FILE__).'/query.php');

class MySQL extends Query {
    public function toString() {
        $this->validateParts();

        $sql = '';
        if ($this->queryType === self::TYPE_SELECT) {
            $sql .= 'SELECT ';

            // Add columns
            foreach ($this->parts[self::PART_COLUMNS] as $tableName => $columns) {
                if (is_array($columns)) {
                    foreach ($columns as $alias => $column) {
                        $sql .= '`'.$tableName.'`.`'.$column.'`';

                        if (is_string($alias)) {
                            $sql .= ' AS `'.$alias.'`';
                        }

                        $sql .= ',';
                    }

                    // Strip trailing comma for this table's columns
                    $sql = substr($sql, 0, -1);
                } else if ($columns === '*') {
                    $sql .= '`'.$tableName.'`.*';
                }

                $sql .= ',';
            }

            // Strip trailing comma, replace with space
            $sql = substr($sql, 0, -1).' ';

            // Add FROM
            if (is_string($this->parts[self::PART_FROM])) {
                $sql .= self::PART_FROM.' `'.$this->parts[self::PART_FROM].'`';
            } else if ($this->parts[self::PART_FROM] instanceof Query) {
                $sql .= self::PART_FROM.' ('.$this->parts[self::PART_FROM]->toString().')';
            }

            // Add joins
            if (sizeof($this->parts[self::PART_JOIN]) > 0) {
                $sql .= ' ';
                foreach ($this->parts[self::PART_JOIN] as $join) {
                    $sql .= $join['type'].' ';

                    if (is_array($join['table']) && sizeof($join['table']) == 1) {
                    	$alias = key($join['table']);
                    	$tableName = $join['table'][$alias];
                        $sql .= '`'.$tableName.'` AS `'.$alias.'`';
                    } else if (is_string($join['table'])) {
                        $sql .= '`'.$join['table'].'`';
                    } else if ($join['table'] instanceof Query) {
                        $sql .= '('.$join['table']->toString().')';
                    }

                    $sql .= ' ON '.$join['conditions'].' ';
                }

                $sql = substr($sql, 0, -1);
            }

            // Add WHERE
            if (sizeof($this->parts[self::PART_WHERE]) > 0) {
                $sql .= ' '.self::PART_WHERE.' '.implode(' AND ', $this->parts[self::PART_WHERE]);
            }
        } else if ($this->queryType === self::TYPE_INSERT) {
            $sql .= 'INSERT ';

            // Add INTO
            $sql .= self::PART_INTO.' `'.$this->parts[self::PART_INTO].'` (';

            // Add columns list
            foreach ($this->parts[self::PART_COLUMNS] as $column) {
                $sql .= '`'.$column.'`,';
            }

            $sql = substr($sql, 0, -1).') '.self::PART_VALUES.' ';

            foreach ($this->parts[self::PART_VALUES] as $rowKey => $row) {
                $sql .= '(';

                foreach ($this->parts[self::PART_COLUMNS] as $columnKey => $column) {
                    if (isset($row[$column])) {
                        $placeholderName = 'r'.$rowKey.'c'.$columnKey;

                        $sql .= ':r'.$rowKey.'c'.$columnKey.',';
                        $this->data[$placeholderName] = $row[$column];
                    } else {
                        $sql .= 'NULL,';
                    }
                }

                $sql = substr($sql, 0, -1).'),';
            }

            $sql = substr($sql, 0, -1);
        }

        return $sql.';';
    }
}
