<?php

include_once('MySQL_Database_TestCase.php');

class SGQL_MySQL_Database_TestCase extends SGQL\MySQL_Database_TestCase {
	protected static $config = [
		'database' => 'mysql',
		'mode' => 'closed',
		'host' => [
			'host' => 'localhost',
			'username' => 'sgql_test_data',
			'password' => 'sgql',
			'charset' => 'utf8',
		]
	];

	protected static $database = 'sgql_unittests_data_1';
}