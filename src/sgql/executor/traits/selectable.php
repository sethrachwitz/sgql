<?php

namespace SGQL\Executor;

use SGQL\Query;

trait Selectable {
    public function executeSelect() {
		$export = $this->query->export();

		$schemaName = key($export);
		$schemaDetails = $export[$schemaName];

		$this->results = [$schemaName => $this->_select($schemaName, $schemaDetails)];

		return $this->results;
    }

    private function _select($schemaName, $schemaDetails, $parentSchemaName = null) {
		$columns = $schemaDetails[Query::PART_COLUMNS];

		$namespaces = [];
		if (isset($schemaDetails['namespaces'])) {
			$namespaces = $schemaDetails['namespaces'];
		}

		$schema = $this->graph->getSchema($schemaName);
		$primaryColumn = $schema->getPrimaryColumn();

		$primaryNotInColumns = false;
		// Make sure that we're getting the primary column for this table, even if it isn't supposed to be returned, so we can associate records properly
		if (!in_array($primaryColumn, $columns)) {
			$primaryNotInColumns = true;
			$columns[] = $primaryColumn;
		}

		if (is_null($parentSchemaName)) {
			$rowsQuery = $this->driver->newQuery()
				->select([$schemaName => $columns])
				->from($schemaName);
		} else {
			$association = $this->graph->getAssociation($schemaName, $parentSchemaName);
			$associationTable = $association->getTableName();

			// Only fetch records that are associated to a record in the parent schema
			$rowsQuery = $this->driver->newQuery()
				->select([
					$schemaName => $columns,
					$associationTable => [
						'associated_id' => $association->getColumnName($parentSchemaName),
					],
				])
				->from($schemaName)
				->join(
					$associationTable,
					$schemaName.'.'.$primaryColumn.' = '.$associationTable.'.'.$association->getColumnName($schemaName),
					\SGQL\Lib\Query\Query::RIGHT_JOIN
				);
		}

		$results = $this->driver->fetchAll($rowsQuery);

		// Fetch nested schema results
		foreach ($namespaces as $childSchemaName => $childSchemaDetails) {
			$associatedRows = $this->_select($childSchemaName, $childSchemaDetails, $schemaName);

			$indexedRows = [];
			foreach ($associatedRows as $row) {
				// Don't return information joined to this row that shouldn't be returned
				$formattedRow = $row;
				unset($formattedRow['associated_id']);

				$indexedRows[$row['associated_id']][] = $formattedRow;
			}

			foreach ($results as $key => $result) {
				if (!isset($result[$primaryColumn])) { // For some reason, the result row doesn't have the primary column - we can't associate rows to this row
					$results[$key][$childSchemaName] = [];
					continue;
				}

				if (isset($indexedRows[$result[$primaryColumn]])) { // Check if there are any associated records
					$results[$key][$childSchemaName] = $indexedRows[$result[$primaryColumn]];
				} else {
					$results[$key][$childSchemaName] = [];
				}
			}
		}

		if ($primaryNotInColumns) {
			foreach ($results as $key => $result) {
				unset($results[$key][$primaryColumn]);
			}
		}

		return $results;
	}
}
