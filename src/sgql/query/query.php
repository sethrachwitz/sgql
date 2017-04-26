<?php

namespace SGQL;

use SGQL\Lib\Graph as Graph;
use SGQL\Lib\Drivers as Drivers;

include_once(dirname(__FILE__).'/traits/assignable.php');
include_once(dirname(__FILE__).'/traits/transformable.php');
include_once(dirname(__FILE__).'/traits/validatable.php');

class Query {
    use Assignable;
    use Transformable;
    use Validatable;

    const TYPE_CREATE = "CREATE";
    const TYPE_SELECT = "SELECT";
    const TYPE_INSERT = "INSERT";

    const QUERY_TYPES = [self::TYPE_CREATE, self::TYPE_SELECT, self::TYPE_INSERT];

	const ASSOCIATION_TYPE_ONE_TO_ONE = Graph\Association::TYPE_ONE_TO_ONE;
	const ASSOCIATION_TYPE_MANY_TO_ONE = Graph\Association::TYPE_MANY_TO_ONE;
	const ASSOCIATION_TYPE_MANY_TO_MANY = Graph\Association::TYPE_MANY_TO_MANY;

	const ASSOCIATION_TYPES = [
		self::ASSOCIATION_TYPE_ONE_TO_ONE,
		self::ASSOCIATION_TYPE_MANY_TO_ONE,
		self::ASSOCIATION_TYPE_MANY_TO_MANY,
	];

    const PART_COLUMNS = 'columns';
    const PART_VALUES = 'values';
    const PART_ASSOCIATION = 'association';

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

    public function createAssociation($parent, $type, $child) {
    	$this->setQueryType(self::TYPE_CREATE);
    	$this->assignAssociation($parent, $type, $child);

    	return $this;
    }

    public function select(array $select) {
        $this->setQueryType(self::TYPE_SELECT);
        $this->assignColumns($select);

        return $this;
    }

    public function insert(array $values, array $associations = []) {
    	// Auto increment values must be consecutive because the code depends on it
	    if (!$this->driver->autoIncrementIsConsecutive()) {
		    throw new \Exception("The auto increment settings for this connection do not guarantee consecutive IDs.  Please " .
			    "update your settings to guarantee that bulk inserts will have consecutive IDs.");
	    }

    	$this->setQueryType(self::TYPE_INSERT);
    	$this->assignValues($values);

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
