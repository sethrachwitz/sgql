<?php

namespace SGQL\Lib\Drivers;

include_once(dirname(__FILE__).'/../config/database/database.php');

use SGQL\Lib\Config as Config;
use SGQL\Lib\Query as Query;

abstract class Driver {
    protected $database;
    protected $dbName;
    protected $host;
    protected $connection;

    function __construct($hosts) {
        $this->database = new Config\Database($hosts);
    }

    function __destruct() {
        $this->close();
    }

    // Returns true if host changed, false if it didn't
    public function useDatabase($database) {
        $this->dbName = $database;

        if (is_null($this->host) || !$this->host->hasDatabase($database)) {
            $this->host = $this->database->getHost($database);
            return true;
        }

        return false;
    }

    public function query($query) {
        $queryStr = $this->queryChecks($query);

        $stmt = $this->connection->prepare($queryStr);

        if (is_string($query)) {
        	$stmt->execute();
		} else {
			$stmt->execute($query->getData());
		}

        return $stmt;
    }

    public function fetchAll($query) {
        $result = $this->query($query);
        return $result->fetchAll();
    }

    protected function queryChecks($query) {
        if (is_null($this->dbName)) {
            throw new \Exception("No database selected");
        }

        if ($query instanceof Query\Query) {
            $queryStr = $query->toString();
        } else if (is_string($query)) {
            $queryStr = $query;
        } else {
            throw new \Exception("Invalid query type");
        }

        return $queryStr;
    }

    public function getDatabaseName() {
        return $this->dbName;
    }

    abstract protected function open();
    abstract protected function close();
    abstract public function beginTransaction();
    abstract public function rollback();
    abstract public function commit();
    abstract public function autoIncrementIsConsecutive();

    abstract public function newQuery();
}
