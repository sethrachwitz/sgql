<?php

namespace SGQL\Lib\Drivers;

include_once(dirname(__FILE__).'/../../../../src/lib/drivers/mysql.php');
include_once(dirname(__FILE__).'/../../../MySQL_Database_TestCase.php');

class UpdateTest extends \SGQL\MySQL_Database_TestCase {
	protected $fixture = [
		[
			'default1Wireframe.sql',
			'default1Data.sql',
		],
		true
	];

	public function testUpdate() {
		$driver = new MySQL(self::$hosts);
		$driver->useDatabase('sgql_unittests_data_1');

		$query = $driver->newQuery()
			->update('orders')
			->set([
				'cost' => 24,
				'shipped' => 0,
			])
			->where('id = 1');

		$results = $driver->query($query);

		$this->assertEquals(1, $results->affectedRows());
	}
}
