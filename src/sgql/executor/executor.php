<?php

namespace SGQL\Executor;

use SGQL\Query;

include_once(dirname(__FILE__) . '/traits/createable.php');
include_once(dirname(__FILE__) . '/traits/destroyable.php');
include_once(dirname(__FILE__) . '/traits/selectable.php');
include_once(dirname(__FILE__) . '/traits/insertable.php');
include_once(dirname(__FILE__).'/../query/query.php');

class Executor {
	use Createable;
	use Destroyable;
	use Selectable;
	use Insertable;

    protected $query;
    protected $graph;

    /** @var \SGQL\Lib\Drivers\Driver  */
    protected $driver;
    protected $results;

    function __construct(Query $query) {
        $this->query = $query;
        $this->driver = $query->getDriver();
		$this->graph = $query->getGraph();
    }

    public function execute() {
    	switch($this->query->getQueryType()) {
		    case Query::TYPE_CREATE:
		    	$this->executeCreate();
		    	return $this->results;
		    	break;
		    case Query::TYPE_DESTROY:
		    	$this->executeDestroy();
		    	return $this->results;
		    	break;
			case Query::TYPE_SELECT:
				$this->executeSelect();
				return $this->results;
				break;
		    case Query::TYPE_INSERT:
		    	$this->executeInsert();
		    	return $this->results;
		    	break;
			default:
				throw new \Exception("Unknown query type '".$this->query->getQueryType()."'");
				break;
		}
	}
}
