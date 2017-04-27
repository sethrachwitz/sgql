<?php

namespace SGQL\Lib\Drivers;

use SGQL\Lib\Query as Query;

include_once(dirname(__FILE__).'/driver.php');
include_once(dirname(__FILE__).'/resultSet/mysql.php');
include_once(dirname(__FILE__).'/../query/mysql.php');

class MySQL extends Driver {
    protected function open() {
        $dsn = 'mysql:host='.$this->host->getHost().';dbname='.$this->dbName.';charset='.$this->host->getCharset();
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->connection = new \PDO($dsn, $this->host->getUsername(), $this->host->getPassword(), $opt);
    }

    protected function close() {
        $this->connection = null;
    }

    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    public function rollback() {
        $this->connection->rollback();
    }

    public function commit() {
        $this->connection->commit();
    }

    public function autoIncrementIsConsecutive() {
    	try {
		    $results = $this->query("SELECT @@GLOBAL.innodb_autoinc_lock_mode AS mode;")->fetchAll();
	    } catch (\PDOException $e) {
    		return false;
	    }

    	if (count($results) == 0) {
    		return false;
	    }

	    return ($results[0]['mode'] != 2); // Mode 2 is interleaved mode, which does not guarantee consecutive IDs
    }

	public function query($query, $bind = []) {
        $result = parent::query($query, $bind);

        return new MySQL_Result_Set($result, $this->connection->lastInsertId());
    }

    public function newQuery() {
        return new Query\MySQL();
    }

    public function useDatabase($database) {
        // PDO does not support database switching, so close the connection, switch the database, and open a new connection
        $this->close();
        parent::useDatabase($database);
        $this->open();
    }
}
