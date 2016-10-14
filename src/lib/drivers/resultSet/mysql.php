<?php

namespace SGQL\Lib\Drivers;

include_once(dirname(__FILE__).'/abstract.php');

class MySQL_Result_Set extends Abstract_Result_Set {
    function __construct($result, $startInsertId = null, $affectedRows = null) {
        $this->result = $result;
        $this->startInsertId = $startInsertId;
        $this->affectedRows = $result->rowCount();
    }

    public function fetchAll() {
        return $this->result->fetchAll();
    }

    public function startInsertId() {
        return $this->startInsertId;
    }

    public function affectedRows() {
        return $this->affectedRows;
    }
}
