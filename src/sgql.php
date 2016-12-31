<?php

include_once(dirname(__FILE__).'/lib/drivers/mysql.php');
include_once(dirname(__FILE__).'/lib/graph/graph.php');
include_once(dirname(__FILE__).'/sgql/query/query.php');
include_once(dirname(__FILE__).'/sgql/parser/parser.php');
include_once(dirname(__FILE__).'/sgql/executor/types/select.php');

use SGQL\Lib\Config as Config;
use SGQL\Lib\Drivers as Drivers;

class SGQL {
    const VERSION = 'a.0.1';
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
        $this->graph = new Config\Graph($this->driver);
    }

    public function initialize() {
    	$this->graph->initialize();
	}

    public function setMode($mode) {
    	$this->graph->setMode($mode);
	}

    public function newQuery($query = null) {
        if (!$this->graph->isInitialized()) {
            throw new Exception("Can't create queries for databases that haven't been initialized for SGQL");
        }
        return new SGQL\Query($query, $this->graph, $this->driver);
    }
}
