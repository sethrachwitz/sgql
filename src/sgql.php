<?php

include_once(dirname(__FILE__).'/lib/drivers/mysql.php');
include_once(dirname(__FILE__).'/lib/graph/graph.php');
include_once(dirname(__FILE__).'/lib/graph/association.php');
include_once(dirname(__FILE__).'/sgql/query/query.php');
include_once(dirname(__FILE__).'/sgql/parser/parser.php');
include_once(dirname(__FILE__).'/sgql/executor/executor.php');

use SGQL\Lib\Graph as Graph;
use SGQL\Lib\Drivers as Drivers;

use SGQL\Query;
use SGQL\Executor;

class SGQL {
    const VERSION = 'a.0.1';

    const ASSOCIATION_TYPE_ONE_TO_ONE = '-';
    const ASSOCIATION_TYPE_MANY_TO_ONE = '<-';
    const ASSOCIATION_TYPE_MANY_TO_MANY = '<->';

    private $driver;
    private $graph;

    function __construct(array $config, $database) {
        if ($config['database'] == 'mysql') {
        	// The query drivers accept a regex for the database, but each SGQL object only runs queries for one database
        	$config['host']['dbRegex'] = $database;

            $this->driver = new Drivers\MySQL([$config['host']]);
        } else {
            throw new Exception("Invalid database type");
        }

        $this->driver->useDatabase($database);
        $this->graph = new Graph\Graph($this->driver);

        if (isset($config['mode'])) {
        	$this->graph->setMode($config['mode']);
		}
    }

    public function initialize() {
    	$this->graph->initialize();
	}

    public function newQuery($query = null) {
        if (!$this->graph->isInitialized()) {
            throw new Exception("Can't create queries for databases that haven't been initialized for SGQL");
        }
        return new SGQL\Query($query, $this->graph, $this->driver);
    }

    public function query(Query $query) {
		$executor = new Executor\Executor($query);
		return $executor->execute();
	}
}
