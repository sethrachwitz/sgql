<?php

namespace SGQL\Lib\Graph;

class Association {
    const TYPE_ONE_TO_ONE = 0;
    const TYPE_MANY_TO_ONE = 1;
    const TYPE_MANY_TO_MANY = 2;

    const ASSOCIATION_TYPES = [self::TYPE_ONE_TO_ONE, self::TYPE_MANY_TO_ONE, self::TYPE_MANY_TO_MANY];

    private $schema1, $schema2;
    private $type;
	private $id;

    function __construct(Schema $schema1, Schema $schema2, $type, $id = null) {
        $this->schema1 = $schema1;
        $this->schema2 = $schema2;

        if (!is_null($id) && !is_numeric($id)) {
        	throw new \Exception("Association IDs must be numeric");
		}

		$this->id = $id;

        if (!in_array($type, self::ASSOCIATION_TYPES)) {
            throw new \Exception("Association between '".$schema1->getName()."' and '".$schema2->getName()."' has invalid type '".$type."'");
        }

        $this->type = $type;
    }

    public function getTableName() {
    	if (is_null($this->id)) {
    		throw new \Exception("This association has no ID");
		}
    	return 'sgql_association_'.$this->id;
	}

	// Each schema has its own column, this method maps a schema name to its column in the association table
	public function getColumnName($schemaName) {
		if ($this->schema1->getName() == $schemaName) {
			return 'p_id';
		} else if ($this->schema2->getName() == $schemaName) {
			return 'c_id';
		} else {
			throw new \Exception("Schema '".$schemaName."' is not a member of this association");
		}
	}

	public function getId() {
    	return $this->id;
	}

	public function getParent() {
    	return $this->schema1;
	}

	public function getChild() {
    	return $this->schema2;
	}

	public function getType() {
    	return $this->type;
	}
}
