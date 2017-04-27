<?php

namespace SGQL\Lib\Graph;

include_once(dirname(__FILE__).'/schema.php');
include_once(dirname(__FILE__) . '/association.php');
include_once(dirname(__FILE__).'/../../sgql.php');

use SGQL\Lib\Drivers as Drivers;

class Graph {
    const MODE_OPEN = 'open';
    const MODE_CLOSED = 'closed';

    const MODES = [self::MODE_OPEN, self::MODE_CLOSED];

    private $version;
    private $mode;
    private $schemas = [];
    private $associations = [];
    private $driver;
    private $initialized = false;

    private $graphCacheFile;

    function __construct(Drivers\Driver $driver) {
    	$this->driver = $driver;

    	$this->graphCacheFile = '../../../../local/'.$driver->getDatabaseName().'-graph-cache.json';
    	if (file_exists($this->graphCacheFile)) {
			$contents = file_get_contents($this->graphCacheFile);

			$decoded = json_decode($contents, true);
			if ($decoded === false) {
				$this->refreshCache();
			} else {
				// Build schemas / relationships objects
				$this->initialized = true;
			}
		} else {
			$this->refreshCache();
		}
    }

    public function setMode($mode) {
    	if (!in_array($mode, self::MODES)) {
    		throw new \Exception("Invalid mode");
		}

    	$this->mode = $mode;

    	// Run query to update
		// Update cache
	}

    // Turn a non-SGQL database into an SGQL database
    public function initialize() {
    	if ($this->isInitialized()) {
    		return;
		}

    	if ($this->driver instanceof Drivers\MySQL) {
    		$this->driver->beginTransaction();

    		$this->driver->query("CREATE TABLE `sgql_associations` (
				`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`parent_id` int(10) UNSIGNED NOT NULL,
				`child_id` int(10) UNSIGNED NOT NULL,
				`type` tinyint(2) UNSIGNED NOT NULL,
    		    `deleted_time` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
			  UNIQUE KEY `table_ids` (`parent_id`,`child_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    		$this->driver->query("CREATE TABLE `sgql_info` (
				`item` varchar(64) COLLATE utf8_bin NOT NULL,
				`value` varchar(512) COLLATE utf8_bin NOT NULL,
				PRIMARY KEY (`item`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

			$this->driver->query("CREATE TABLE `sgql_tables` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			  	`name` varchar(64) COLLATE utf8_bin NOT NULL,
			  	`primary_column` varchar(64) COLLATE utf8_bin NOT NULL,
			  	PRIMARY KEY (`id`),
			  	UNIQUE KEY `name` (`name`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

			$insertVersionQuery = $this->driver->newQuery()
				->insert('sgql_info')
				->values([
					[
						'item' => 'version',
						'value' => \SGQL::VERSION,
					],
					[
						'item' => 'mode',
						'value' => self::MODE_CLOSED,
					],
				]);

			$this->driver->query($insertVersionQuery);

			$tables = $this->driver->fetchAll("SHOW tables;");

			$addTables = [];
			foreach ($tables as $table) {
				$tableName = $table[key($table)];
				if (substr($tableName, 0, 5) != 'sgql_') {
					$primaryColumn = $this->driver->fetchAll("SHOW KEYS FROM `".$tableName."` WHERE Key_name = 'Primary';")[0];

					if (!is_null($primaryColumn)) {
						$primaryColumnName = $primaryColumn['Column_name'];
						$addTables[] = [
							'name' => $tableName,
							'primary_column' => $primaryColumnName,
						];
					}
				}
			}

			if (count($addTables) > 0) {
				$insertTablesQuery = $this->driver->newQuery()
					->insert('sgql_tables')
					->values($addTables);

				$this->driver->query($insertTablesQuery);
			}

			$this->driver->commit();
		} else {
    		throw new \Exception("Unknown database driver - can't initialize SGQL database");
		}

		$this->refreshCache();
	}

	public function applyUpdates() {
    	// Go in ascending order of versions, and update the version as we go along so that all future updates are
		// applied

		$oldVersion = $this->version;

    	if ($this->version == 'a.0.1') {
			$this->driver->query("ALTER TABLE `sgql_associations` ADD `deleted_time` DATETIME NULL DEFAULT NULL AFTER `type`");
			$this->version = 'a.0.2';
	    }

	    if ($oldVersion != $this->version) {
    		$query = $this->driver->newQuery()
			    ->update('sgql_info')
			    ->set([
			    	'value' => $this->version,
			    ])
			    ->where([
			    	"item = 'version'"
			    ]);

    		$this->driver->query($query);

		    $this->refreshCache();
	    }
	}

	public function flushCache() {
    	// Flush local cache to file
	}

	// Pull SGQL information from database and make a local cached copy
	public function refreshCache() {
    	$requiredTables = ['sgql_info', 'sgql_tables', 'sgql_associations'];

		$tables = $this->driver->fetchAll("SHOW tables;");

		$requiredTablesFound = 0;
		foreach ($tables as $table) {
			$tableName = $table[key($table)];
			if (in_array($tableName, $requiredTables)) {
				$requiredTablesFound++;
			}
		}

		if ($requiredTablesFound < count($requiredTables)) {
			$this->initialized = false;
			return false;
		}

		$sgqlInfoQuery = $this->driver->newQuery()
			->select([
				'sgql_info' => [
					'item',
					'value',
				],
			])
			->from('sgql_info');

		$sgqlInfo = $this->driver->fetchAll($sgqlInfoQuery);

		foreach ($sgqlInfo as $info) {
			if ($info['item'] == 'mode') {
				$this->mode = $info['value'];
			} else if ($info['item'] == 'version') {
				$this->version = $info['value'];
			}
		}

		// Apply updates here so we have an up-to-date version, but the other tables aren't read yet
		$this->applyUpdates();

		$sgqlTablesQuery = $this->driver->newQuery()
			->select([
				'sgql_tables' => [
					'id',
					'name',
					'primary_column',
				],
			])
			->from('sgql_tables');

		$sgqlTables = $this->driver->fetchAll($sgqlTablesQuery);

		$this->schemas = [];
		foreach ($sgqlTables as $table) {
			$this->schemas[$table['name']] = new Schema($table['id'], $table['name'], $table['primary_column']);
		}

		$sgqlAssociationsQuery = $this->driver->newQuery()
			->select([
				'sgql_associations' => [
					'id',
					'parent_id',
					'child_id',
					'type',
					'deleted_time',
				],
				'st1' => [
					'parent_name' => 'name',
				],
				'st2' => [
					'child_name' => 'name',
				],
			])
			->from('sgql_associations')
			->join(['st1' => 'sgql_tables'], 'sgql_associations.parent_id = st1.id')
			->join(['st2' => 'sgql_tables'], 'sgql_associations.child_id = st2.id')
			->where('sgql_associations.deleted_time IS NULL');

		$associations = $this->driver->fetchAll($sgqlAssociationsQuery);

		$this->associations = [];
		foreach ($associations as $association) {
			$this->associations[$association['parent_name'].' '.$association['child_name']] = new Association($this->getSchema($association['parent_name']), $this->getSchema($association['child_name']), $association['type'], $association['id']);
		}

		$this->initialized = true;
	}

    public function getSchema($name) {
        if (isset($this->schemas[$name])) {
            return $this->schemas[$name];
        } else {
            throw new \Exception("Schema '".$name."' does not exist");
        }
    }

    public function getAssociation($schema1, $schema2) {
        if (isset($this->associations[$schema1.' '.$schema2])) {
            return $this->associations[$schema1.' '.$schema2];
        } else if (isset($this->associations[$schema2.' '.$schema1])) {
            return $this->associations[$schema2.' '.$schema1];
        } else if ($this->mode == 'open') {
        	return $this->addAssociation($this->getSchema($schema1), $this->getSchema($schema2), Association::TYPE_MANY_TO_MANY);
        } else {
            throw new \Exception("Association between '".$schema1."' and '".$schema2."' was not found");
        }
    }

    public function addAssociation(Schema $schema1, Schema $schema2, $type) {
    	if (!in_array($type, Association::ASSOCIATION_TYPES)) {
    		throw new \Exception("Invalid association type '".$type."'");
		}

		$schema1id = $schema1->getId();
		$schema2id = $schema2->getId();

		$this->driver->beginTransaction();

		$insertAssociation = $this->driver->newQuery()
			->insert('sgql_associations')
			->values([
				[
					'parent_id' => $schema1id,
					'child_id' => $schema2id,
					'type' => $type,
				],
			])
			->onDuplicate([
				'deleted_time' => null,
			]);

		$result = $this->driver->query($insertAssociation);
		$associationId = $result->startInsertId();

		if (is_null($associationId)) {
			$this->driver->rollback();
			throw new \Exception("Association was not created successfully");
		}

		if ($this->driver instanceof Drivers\MySQL) {
			$this->driver->query("CREATE TABLE IF NOT EXISTS `sgql_association_".$associationId."` (
				`p_id` INT NOT NULL,
				`c_id` INT NOT NULL,
				PRIMARY KEY (`p_id`, `c_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

			try {
				if ($type === Association::TYPE_ONE_TO_ONE) {
					// Both the parent and child ID have to be unique for true 1-1
					$this->driver->query("CREATE UNIQUE INDEX parent_id ON `sgql_association_".$associationId."` (`p_id`);");
					$this->driver->query("CREATE UNIQUE INDEX child_id ON `sgql_association_".$associationId."` (`c_id`);");
				} else if ($type === Association::TYPE_MANY_TO_ONE) {
					// If the child ID is unique, it can have at most 1 parent (N-1)
					$this->driver->query("CREATE UNIQUE INDEX child_id ON `sgql_association_".$associationId."` (`c_id`);");
				} else if ($type === Association::TYPE_MANY_TO_MANY) {
					// If the tuple is unique within the table, it prevents duplicates but allows any combination
					$this->driver->query("CREATE UNIQUE INDEX tuple ON `sgql_association_".$associationId."` (`p_id`, `c_id`);");
				}
			} catch (\PDOException $e) {
				// If this is error 42000, it means that the index already exists, which is fine
				if ($e->getCode() != 42000) throw $e;
			}

			// @TODO: Add foreign key restraint on the association table
		}

		$this->driver->commit();

		$association = new Association($schema1, $schema2, $type, $associationId);
		$this->associations[$schema1->getName().' '.$schema2->getName()] = $association;

		$this->flushCache();

    	return $association;
	}

	public function destroyAssociation(Schema $schema1, Schema $schema2) {
		$association = $this->getAssociation($schema1->getName(), $schema2->getName());

		$query = $this->driver->newQuery()
			->update('sgql_associations')
			->set([
				'deleted_time = NOW()',
			])
			->where('id = :id')
			->bind([
				'id' => $association->getId(),
			]);

		$this->driver->query($query);

		$this->refreshCache();
	}

    public function schemaExists($name) {
        try {
            $this->getSchema($name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function associationExists($schema1, $schema2) {
        try {
            $this->getAssociation($schema1, $schema2);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getAssociations() {
    	return $this->associations;
    }

    public function namespaceExists(array $namespace) {
        try {
            $this->getNamespace($namespace);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getNamespace(array $namespace) {
        if (sizeof($namespace) <= 1) {
            return false;
        }

        $namespaceInfo = [];

        $current = null;
        foreach ($namespace as $schema) {
            if (is_null($current)) {
                $current = $this->getSchema($schema);
            } else if (is_string($schema)) {
                try {
                    $namespaceInfo[] = $this->getAssociation($current->getName(), $schema);
                } catch (\Exception $e) {
                    throw new \Exception("Namespace '".implode('.', $namespace)."' does not exist");
                }

                $current = $this->getSchema($schema);
            } else {
                throw new \Exception("Invalid schema name");
            }
        }

        return $namespaceInfo;
    }

    public function isInitialized() {
    	return $this->initialized;
	}
}
