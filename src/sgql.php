<?php

include_once(dirname(__FILE__).'/lib/drivers/mysql.php');
include_once(dirname(__FILE__).'/lib/graph/graph.php');
include_once(dirname(__FILE__).'/lib/graph/association.php');
include_once(dirname(__FILE__).'/sgql/query/query.php');
include_once(dirname(__FILE__).'/sgql/parser/parser.php');
include_once(dirname(__FILE__).'/sgql/executor/types/select.php');

use SGQL\Lib\Graph as Graph;
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

    public function createAssociation($schema1, $type, $schema2) {
    	switch ($type) {
			case '-':
				$type = SGQL\Lib\Graph\Association::TYPE_ONE_TO_ONE;
				break;
			case '<-':
				$type = SGQL\Lib\Graph\Association::TYPE_MANY_TO_ONE;
				break;
			case '<->':
				$type = SGQL\Lib\Graph\Association::TYPE_MANY_TO_MANY;
				break;
			default:
				throw new \Exception("Invalid association type '".$type."'");
		}

    	return $this->graph->addAssociation($this->graph->getSchema($schema1), $this->graph->getSchema($schema2), $type);
	}
}
