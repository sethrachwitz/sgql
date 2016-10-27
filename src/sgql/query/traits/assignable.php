<?php

namespace SGQL;

trait Assignable {
    protected function assignColumns(array $columns) {
        $validated = $this->validateColumns($columns);

        $topLevelSchema = key($validated);

        $this->_assignColumns($validated[$topLevelSchema], [$topLevelSchema]);
    }

    private function _assignColumns(array $schema, array $namespace) {
        $pointer = &$this->getNamespacePointer($namespace, true);

        foreach ($schema['columns'] as $alias => $column) {
            if (is_string($alias)) {
                $pointer[Query::PART_COLUMNS][$alias] = $column;
            } else {
                $pointer[Query::PART_COLUMNS][] = $column;
            }
        }

        if (isset($schema['namespaces']) && sizeof($schema['namespaces']) > 0) {
            foreach ($schema['namespaces'] as $schemaName => $schema) {
                $this->_assignColumns($schema, array_merge($namespace, [$schemaName]));
            }
        }
    }

    private function &getNamespacePointer(array $namespace, $createIfNonexistant = false) {
        $pointer = null;

        if (!isset($this->query[$namespace[0]])) {
            if (!$createIfNonexistant) {
                throw new \Exception("Namespace '".implode('.', $namespace)."' does not exist");
            }

            $this->query[$namespace[0]] = [];
        } else if (key($this->query) != $namespace[0]) {
            throw new \Exception("Schema '".$namespace[0]."' is not the top level schema");
        }

        $pointer = &$this->query[$namespace[0]];

        unset($namespace[0]);

        if (sizeof($namespace) > 0) {
            foreach ($namespace as $schema) {
                if (!$createIfNonexistant) {
                    throw new \Exception("Namespace '".implode('.', $namespace)."' does not exist");
                }

                if (!isset($pointer['namespaces'])) {
                    $pointer['namespaces'] = [];
                }

                if (!isset($pointer['namespaces'][$schema])) {
                    $pointer['namespaces'][$schema] = [];
                }

                $pointer = &$pointer['namespaces'][$schema];
            }
        }

        return $pointer;
    }
}
