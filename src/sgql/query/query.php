<?php

namespace SGQL;

use SGQL\Lib\Graph as Graph;
use SGqL\Lib\Drivers as Drivers;

include_once(dirname(__FILE__).'/traits/assignable.php');
include_once(dirname(__FILE__).'/traits/transformable.php');
include_once(dirname(__FILE__).'/traits/validatable.php');

class Query {
    use Assignable;
    use Transformable;
    use Validatable;

    const TYPE_SELECT = "SELECT";

    const QUERY_TYPES = [self::TYPE_SELECT];

    const PART_COLUMNS = 'columns';

    private $parser;
    private $query = [];

    private $queryType;
    private $data = [];

    // Comes from the SGQL parent creator
    private $graph;
    private $driver;

    function __construct($query = null, Graph\Graph $graph, Drivers\Driver $driver) {
		$this->graph = $graph;
		$this->driver = $driver;

        if (!is_null($query)) {
            if (!is_string($query)) {
                throw new \Exception("Query must be a string");
            } else {
                $this->transform($query);
            }
        }
    }

    public function getDriver() {
    	return $this->driver;
	}

	public function getGraph() {
    	return $this->graph;
	}

    public function updateDriver(Drivers\Driver $driver) {
        $this->driver = $driver;
    }

    public function getData() {
        return $this->data;
    }

    public function getQueryType() {
        return $this->queryType;
    }

    public function select(array $select) {
        $this->setQueryType(self::TYPE_SELECT);
        $this->assignColumns($select);

        return $this;
    }

    public function bind(array $data) {
        $this->data = $data;
        return $this;
    }

    private function setQueryType($type) {
        if (!is_null($this->queryType)) {
            throw new \Exception("Query type has already been set to ".$this->queryType);
        }

        if (!in_array($type, self::QUERY_TYPES)) {
            throw new \Exception("Invalid query type");
        } else {
            $this->queryType = $type;
        }
    }

    public function export() {
        return $this->query;
    }
}
