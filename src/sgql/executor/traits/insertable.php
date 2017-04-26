<?php

namespace SGQL\Executor;

use SGQL\Lib\Drivers\Abstract_Result_Set;
use SGQL\Lib\Drivers\Driver;
use SGQL\Lib\Graph\Association;
use SGQL\Parser;
use SGQL\Query;

trait Insertable {
	public function executeInsert() {
		$export = $this->query->export();

		$values = $export[Parser::TOKEN_VALUES];
		$this->results['values'] = $this->_insert($values);

		return $this->results;
	}

	private function _insert($values) {
		$this->driver->beginTransaction();

		try {
			reset($values);
			$topLevelSchemaName = key($values);

			$transformedValues = [$topLevelSchemaName => self::flattenValues($values[$topLevelSchemaName])];

			$result = $this->insertValues($transformedValues[$topLevelSchemaName], [$topLevelSchemaName]);

			$this->driver->commit();

			return [$topLevelSchemaName => $result];
		} catch (\Exception $e) {
			$this->driver->rollback();
			throw $e;
		}
	}

	private function insertValues($schema, $namespace) {
		try {
			$schemaName = $namespace[count($namespace) - 1];

			if (!isset($schema['values']) || count($schema['values']) == 0) {
				throw new \Exception("No values to insert");
			}

			$values = $schema['values'];

			$insert = [];
			foreach ($values as $value) {
				$insert[] = $value['columns'];
			}

			// Insert this schema's values
			$query = $this->driver->newQuery()
				->insert($schemaName)
				->values($insert);

			/** @var Abstract_Result_Set $results */
			$results = $this->driver->query($query);

			// Check that we inserted the correct number of rows
			if ($results->affectedRows() != count($insert)) {
				throw new \Exception("Only ".count($insert)." out of ".$results->affectedRows()." rows inserted successfully");
			}

			// Add the primary column and its new auto-increment value to the result set
			$primaryColumn = $this->graph->getSchema($schemaName)->getPrimaryColumn();
			$id = $results->startInsertId();

			foreach ($values as &$value) {
				$value['columns'][$primaryColumn] = $id++; // Increment so the next row has the next ID
			}

			// Insert nested records
			if (isset($schema['namespaces'])) {
				$nestedSchemas = $schema['namespaces'];
				foreach ($nestedSchemas as $nestedSchemaName => $nestedSchema) {
					$associateIds = []; // Stores associations between records in this schema and nested values

					$nestedValues = $this->insertValues($nestedSchema, array_merge($namespace, [$nestedSchemaName]));
					foreach ($nestedValues as $nestedValue) {
						$values[$nestedValue['parent_id']]['columns'][$nestedSchemaName][] = $nestedValue['columns'];

						$nestedSchemaPrimaryColumnName = $this->graph->getSchema($nestedSchemaName)->getPrimaryColumn();
						$associateIds[] = [
							'parent_id' => $values[$nestedValue['parent_id']]['columns'][$primaryColumn],
							'child_id' => $nestedValue['columns'][$nestedSchemaPrimaryColumnName],
						];
					}

					$this->associateValues($schemaName, $nestedSchemaName, $associateIds);
				}
			}

			if (sizeof($namespace) == 1) { // Recursion is over, so move each value out of
				foreach ($values as &$value) {
					$value = $value['columns'];
				}
			}

			return $values;
		} catch (\Exception $e) {
			// Check for JSON message, indicating that this was caused somewhere in recursion
			$decoded = json_decode($e->getMessage(), true);

			if (sizeof($namespace) == 1) {
				if (!is_null($decoded)) {
					// The exception was not thrown in the top level namespace, and has bubbled up
					$namespace = $decoded['namespace'];
					$message = $decoded['message'];
				} else {
					$message = $e->getMessage();
				}

				throw new \Exception("There was an error executing your query in namespace '".implode('.', $namespace)."': ".$message);
			} else {
				if (!is_null($decoded)) {
					// The exception was not thrown here, so bubble it up
					throw $e;
				}

				throw new \Exception(json_encode(['namespace' => $namespace, 'message' => $e->getMessage()]));
			}
		}
	}

	/**
	 * @param string    $thisSchemaName         Schema name of the record we are associating to
	 * @param string    $valuesSchemaName       Schema name of the records we are associating
	 * @param array     $values                 IDs of the records we're associating
	 * @param bool      $insert                 If an insert, do a lazy check to see if we're violating the association type
	 */
	public function associateValues($thisSchemaName, $valuesSchemaName, array $values, $insert = true) {
		/** @var Association $association */
		$association = $this->graph->getAssociation($thisSchemaName, $valuesSchemaName);

		$flipped = ($association->getParent()->getName() == $valuesSchemaName);

		$thisColumn = ($flipped ? 'c_id' : 'p_id');
		$valuesColumn = ($flipped ? 'p_id' : 'c_id');

		$associations = [];
		foreach ($values as $value) {
			// @TODO: Check that we aren't violating the association type
			$associations[] = [
				$thisColumn => $value['parent_id'],
				$valuesColumn => $value['child_id'],
			];
		}

		$query = $this->driver->newQuery()
			->insert($association->getTableName())
			->values($associations);

		$this->driver->query($query);
	}

	/**
	 * Takes an array of values in nested form (each value sits under the value it is to be associated with), and
	 * transforms it into an easily insertable form where all of the values have been "flattened" into namespaces, with
	 * internal IDs that point back to their associated parent value.  This has the benefit of having all nested values for a given
	 * namespace in the same place, so we can insert them all in one query, and associate them in another, rather than needing to
	 * run many queries for the same namespace.
	 *
	 * The internal pointer is necessary because the resulting array cannot be built with recursion.  The general structure can be
	 * replicated, but the array indexes will change during recursive array merges, causing parent_id to be incorrect.  This would
	 * lead to problems when the associations are created.
	 *
	 * @param array $values
	 * @param array &$pointer           An internal pointer of sorts, which allows recursive calls to interact with a "global" variable
	 * @param int   $parentId           The parent value that all of the $values should be associated with
	 * @return array Namespaced values in the form:
	 *  [
	 *      [schema] => [
	 *          [values] => [
	 *              [
	 *                  [internalId] => 123,
	 *                  ...
	 *              ]
	 *          ],
	 *          [namespaces] => [
	 *              [schema] => [
	 *                  ...
	 *              ]
	 *          ]
	 *      ]
	 *  ]
	 */
	public static function flattenValues(array $values, &$pointer = [], $parentId = null) {
		// For each value, decompose it into columns (the actual values for this schema), and recursively associate nested
		// records to this one
		foreach ($values as $row => $value) {
			// Set the columns for this value
			$pointer['values'][] = [
				'columns' => $value['columns'],
			];

			// Get the array index of the just-inserted value
			$thisId = max(array_keys($pointer['values']));

			// If this record has a parent, set that value so we can trace it back later
			if (!is_null($parentId)) {
				$pointer['values'][$thisId]['parent_id'] = $parentId;
			}

			// If this value has nested values, recursively associate them with this record
			if (isset($value['namespaces'])) {
				foreach ($value['namespaces'] as $schema => $schemaValues) {
					// Get a pointer to where these records are going to be placed
					$schemaPointer = &$pointer['namespaces'][$schema];

					// Recursive call to flattenValues, which passes the actual values, where they need to be placed, and
					// the ID of this record so they can be associated properly after being flattened
					self::flattenValues($schemaValues, $schemaPointer, $thisId);
				}
			}
		}

		return $pointer;
	}
}