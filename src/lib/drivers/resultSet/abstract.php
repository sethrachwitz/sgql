<?php

namespace SGQL\Lib\Drivers;

abstract class Abstract_Result_Set {
    protected $results;
    protected $startInsertId;
    protected $affectedRows;

    abstract public function __construct($result, $startInsertId, $affectedRows);

    abstract public function fetchAll();
    abstract public function startInsertId();
    abstract public function affectedRows();
}
