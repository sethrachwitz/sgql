<?php

namespace SGQL\Lib\Graph;

class Schema {
	private $id;
    private $name;
    private $primaryColumn;

    public function __construct($id, $name, $primaryColumn) {
    	$this->id = $id;
        $this->name = $name;
        $this->primaryColumn = $primaryColumn;
    }

    public function getId() {
    	return $this->id;
	}

    public function getName() {
        return $this->name;
    }

    public function getPrimaryColumn() {
        return $this->primaryColumn;
    }
}
