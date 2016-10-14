<?php

namespace SGQL\Lib\Config;

include_once(dirname(__FILE__).'/host.php');

class Database {
    private $hosts = [];
    private $cantConnect = [];

    function __construct(array $hosts) {
        foreach ($hosts as $key => $host) {
            $this->hosts[$key] = new Host($key, @$host['host'], @$host['username'], @$host['password'], @$host['charset'], @$host['dbRegex']);
        }

        if (sizeof($this->hosts) == 0) {
            throw new \Exception("There must be at least one host configured");
        }
    }

    public function getHost($database = null) {
        // Randomize access to hosts
        $numHosts = sizeof($this->hosts);

        if ($numHosts == 1) {
            if (is_null($database) || $this->hosts[0]->hasDatabase($database)) {
                return $this->hosts[0];
            }
        } else {
            $seen = [];
            $host = null;
            while (sizeof($seen) < $numHosts) {
                // Loop until an ID is found that hasn't been tried yet
                while (in_array($id = rand(0, $numHosts - 1), $seen));

                if (is_null($database) || $this->hosts[$id]->hasDatabase($database)) {
                    return $this->hosts[$id];
                } else {
                    $seen[] = $id;
                }
            }
        }

        throw new \Exception("No hosts have access to database '".$database."'");
    }

    public function cantConnect($id) {
        if (array_key_exists($id, $hosts)) {
            unset($hosts[$id]);
            $this->cantConnect[] = $id;
        }

        if (sizeof($this->hosts) == 0) {
            throw new \Exception("No host can be connected to at this time");
        }
    }
}
