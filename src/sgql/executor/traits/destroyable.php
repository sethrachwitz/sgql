<?php

namespace SGQL\Executor;

use SGQL\Lib\Drivers\Abstract_Result_Set;
use SGQL\Lib\Drivers\Driver;
use SGQL\Lib\Graph\Association;
use SGQL\Parser;
use SGQL\Query;

trait Destroyable {
	public function executeDestroy() {
		$export = $this->query->export();

		if (isset($export[Query::PART_ASSOCIATION])) {
			$info = $export[Query::PART_ASSOCIATION];
			$this->results = $this->_destroyAssociation($info);
		} else {
			throw new \Exception("Invalid destroy type");
		}

		return $this->results;
	}

	private function _destroyAssociation(array $info) {
		if (!isset($info['parent'], $info['child'])) {
			throw new \Exception("Missing parent, type, or child parameters");
		}

		$parent = $info['parent'];
		$type = (isset($info['type']) ? $info['type'] : null);
		$child = $info['child'];

		/** @var Association $association */
		$association = $this->graph->getAssociation($info['parent'], $info['child']);
		$associationTypeString = \SGQL::ASSOCIATION_MAP[$association->getType()];

		if (!is_null($type)) {
			if ($type != $associationTypeString) {
				throw new \Exception("Association between '".$parent."' and '".$child."' is of type '".\SGQL::ASSOCIATION_MAP[$association->getType()]."'");
			}
		}

		$this->graph->destroyAssociation($association->getParent(), $association->getChild());
	}
}