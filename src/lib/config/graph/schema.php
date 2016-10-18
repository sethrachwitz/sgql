<?php

namespace SGQL\Lib\Config;

include_once(dirname(__FILE__).'/column.php');

class Schema {
    private $name;
    private $columns = [];
    private $primaryKey;

    public function __construct($name, array $columns) {
        $this->name = $name;

        $hasPrimary = false;

        foreach ($columns as $columnName => $details) {
            $this->columns[$columnName] = new Column($columnName, $details);

            if (isset($details['index']) && $details['index'] === Column::INDEX_PRIMARY) {
                if (!is_null($this->primaryKey)) {
                    throw new \Exception("Schema '".$name."' already has primary column `".$this->primaryKey."`");
                } else {
                    $this->primaryKey = $columnName;
                }
            }
        }

        if (is_null($this->primaryKey)) {
            throw new \Exception("Schema '".$name."' does not have primary column");
        }
    }

    public function getName() {
        return $this->name;
    }

    public function getPrimaryColumn() {
        return $this->columns[$this->primaryKey];
    }

    public function columnExists($column) {
        return array_key_exists($column, $this->columns);
    }

    public function getColumns() {
        return $this->columns;
    }
}
