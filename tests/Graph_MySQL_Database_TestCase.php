<?php

namespace SGQL;

use SGQL\Lib\Drivers\MySQL;
use SGQL\Lib\Graph as Graph;

include_once('MySQL_Database_TestCase.php');

class Graph_MySQL_Database_TestCase extends MySQL_Database_TestCase {
	protected static $driver;
	protected static $graph;

	public function setUp() {
		parent::setUp();

		self::$driver = new MySQL(self::$hosts);
		self::$driver->useDatabase('sgql_unittests_data_1');

		self::$graph = new Graph\Graph(self::$driver);
		self::$graph->initialize();
		$this->addAssociations();
	}

	public function addAssociations() {
		self::$graph->addAssociation(self::$graph->getSchema('customers'), self::$graph->getSchema('orders'), Graph\Association::TYPE_MANY_TO_ONE);
		self::$graph->addAssociation(self::$graph->getSchema('orders'), self::$graph->getSchema('products'), Graph\Association::TYPE_MANY_TO_MANY);
	}
}