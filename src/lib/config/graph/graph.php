<?php

namespace SGQL\Lib\Config;

include_once(dirname(__FILE__).'/schema.php');
include_once(dirname(__FILE__).'/relationship.php');

class Graph {
    const MODE_OPEN = 'open';
    const MODE_CLOSED = 'closed';

    private $mode;
    private $schemas = [];
    private $relationships = [];

    function __construct($graph) {
        if (!isset($graph['schemas']) || sizeof($graph['schemas']) == 0) {
            throw new \Exception("No schemas defined");
        }

        foreach ($graph['schemas'] as $name => $columns) {
            $this->schemas[$name] = new Schema($name, $columns);
        }

        if (isset($graph['relationships']) && sizeof($graph['relationships']) > 0) {
            foreach ($graph['relationships'] as $relationship) {
                if ($this->getSchema($relationship['parent']) && $this->getSchema($relationship['child'])) {
                    $this->relationships[$relationship['parent'].' '.$relationship['child']] =
                        new Relationship($relationship['parent'], $relationship['child'], $relationship['type']);
                }
            }
        }

        if (!isset($graph['mode']) || ($graph['mode'] !== self::MODE_CLOSED && $graph['mode'] !== self::MODE_OPEN)) {
            throw new \Exception("Invalid graph mode '".$graph['mode']."'");
        } else {
            $this->mode = $graph['mode'];
        }
    }

    public function getSchema($name) {
        if (isset($this->schemas[$name])) {
            return $this->schemas[$name];
        } else {
            throw new \Exception("Schema '".$name."' does not exist");
        }
    }

    public function getRelationship($schema1, $schema2) {
        if (isset($this->relationships[$schema1.' '.$schema2])) {
            return $this->relationships[$schema1.' '.$schema2];
        } else if (isset($this->relationships[$schema2.' '.$schema1])) {
            return $this->relationships[$schema2.' '.$schema1];
        } else if ($this->mode == 'open') {
            return new Relationship($schema1, $schema2, Relationship::TYPE_MANY_TO_MANY);
        } else {
            throw new \Exception("Relationship between '".$schema1."' and '".$schema2."' was not found");
        }
    }

    public function schemaExists($name) {
        try {
            $this->getSchema($name);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function relationshipExists($schema1, $schema2) {
        try {
            $this->getRelationship($schema1, $schema2);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function schemaColumnExists($schema, $column) {
        return ($this->schemaExists($schema) && $this->getSchema($schema)->columnExists($column));
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
                    $namespaceInfo[] = $this->getRelationship($current->getName(), $schema);
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
}
