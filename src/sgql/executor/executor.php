<?php

namespace SGQL\Executor;

use SGQL\Query;

include_once(dirname(__FILE__) . '/traits/selectable.php');
include_once(dirname(__FILE__).'/../query/query.php');

class Executor {
	use Selectable;

    protected $query;
    protected $graph;
    protected $driver;
    protected $results;

    function __construct(Query $query) {
        $this->query = $query;
        $this->driver = $query->getDriver();
		$this->graph = $query->getGraph();
    }

    public function execute() {
    	switch($this->query->getQueryType()) {
			case Query::TYPE_SELECT:
				$this->executeSelect();
				return $this->results;
				break;
			default:
				throw new \Exception("Unknown query type");
				break;
		}
	}
}
