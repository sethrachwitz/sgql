<?php

namespace SGQL\Lib\Query;

include_once(dirname(__FILE__).'/traits/validatable.php');

abstract class Query {
    use Validatable;

    const TYPE_SELECT = 'SELECT';
    const TYPE_INSERT = 'INSERT';

    const LEFT_JOIN = 'LEFT JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';
    const OUTER_JOIN = 'OUTER JOIN';
    const INNER_JOIN = 'INNER JOIN';

    const PART_COLUMNS = 'columns';
    const PART_FROM = 'FROM';
    const PART_INTO = 'INTO';
    const PART_JOIN = 'join';
    const PART_WHERE = 'WHERE';
    const PART_VALUES = 'VALUES';

    const QUERY_TYPES = [self::TYPE_SELECT, self::TYPE_INSERT];
    const JOIN_TYPES = [self::LEFT_JOIN, self::RIGHT_JOIN, self::OUTER_JOIN, self::INNER_JOIN];

    protected $data = [];

    protected $queryType;

    protected $parts = [
        self::PART_COLUMNS => [],
        self::PART_FROM => '',
        self::PART_INTO => '',
        self::PART_JOIN => [],
        self::PART_WHERE => [],
        self::PART_VALUES => [],
    ];

    public function select(array $columns) {
        $this->setQueryType(self::TYPE_SELECT);
        $this->parts[self::PART_COLUMNS] = $this->validateColumns($columns);
        return $this;
    }

    public function insert($table) {
        $this->setQueryType(self::TYPE_INSERT);
        $this->parts[self::PART_INTO] = $this->validateInto($table);
        return $this;
    }

    public function from($table) {
        $this->parts[self::PART_FROM] = $this->validateFrom($table);
        return $this;
    }

    public function join($table, $conditions, $type = self::INNER_JOIN) {
        $this->parts[self::PART_JOIN][] = $this->validateJoin($table, $conditions, $type);
        return $this;
    }

    public function where($condition) {
        $this->parts[self::PART_WHERE][] = $this->validateWhere($condition);
        return $this;
    }

    public function values($values) {
        $this->parts[self::PART_VALUES] = $this->validateValues($values);

        // Set columns
        foreach ($this->parts[self::PART_VALUES] as $row) {
            foreach ($row as $column => $value) {
                if (!in_array($column, $this->parts[self::PART_COLUMNS])) {
                    $this->parts[self::PART_COLUMNS][] = $column;
                }
            }
        }

        return $this;
    }

    public function getData() {
        return $this->data;
    }

    public function bind(array $data) {
        $this->data = $data;
        return $this;
    }

    protected function setQueryType($type) {
        if (!is_null($this->queryType)) {
            throw new \Exception("Cannot redeclare query type");
        }

        if (!in_array($type, self::QUERY_TYPES)) {
            throw new \Exception("Invalid query type");
        } else {
            $this->queryType = $type;
        }
    }

    abstract public function toString();
}
