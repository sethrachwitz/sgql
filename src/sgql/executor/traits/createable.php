<?php

namespace SGQL\Executor;

use SGQL\Lib\Drivers\Abstract_Result_Set;
use SGQL\Lib\Drivers\Driver;
use SGQL\Lib\Graph\Association;
use SGQL\Parser;
use SGQL\Query;

trait Createable {
	public function executeCreate() {
		$export = $this->query->export();

		if (isset($export[Query::PART_ASSOCIATION])) {
			$info = $export[Query::PART_ASSOCIATION];
			$this->results = $this->_createAssociation($info);
		} else {
			throw new \Exception("Invalid create type");
		}

		return $this->results;
	}

	private function _createAssociation(array $info) {
		if (!isset($info['parent'], $info['type'], $info['child'])) {
			throw new \Exception("Missing parent, type, or child parameters");
		}

		$parent = $info['parent'];
		$type = $info['type'];
		$child = $info['child'];

		if ($this->graph->associationExists($info['parent'], $info['child'])) {
			throw new \Exception("Association between '".$parent."' and '".$child."' already exists");
		}

		switch ($type) {
			case '-':
				$type = Association::TYPE_ONE_TO_ONE;
				break;
			case '<-':
				$type = Association::TYPE_MANY_TO_ONE;
				break;
			case '<->':
				$type = Association::TYPE_MANY_TO_MANY;
				break;
			default:
				throw new \Exception("Invalid association type '".$type."'");
		}

		$parentSchema = $this->graph->getSchema($parent);
		$childSchema = $this->graph->getSchema($child);

		return $this->graph->createAssociation($parentSchema, $childSchema, $type);
	}
}