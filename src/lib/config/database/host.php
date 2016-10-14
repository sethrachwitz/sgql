<?php

namespace SGQL\Lib\Config;

class Host {
    public $id;
    public $host;
    public $username;
    public $password;
    public $charset;
    public $dbRegex;

    function __construct($id, $host, $username, $password, $charset, $dbRegex = null) {
        foreach (['host', 'username', 'password', 'charset'] as $key) {
            if (is_null($$key)) {
                throw new \Exception("Host '".$id."' must have a ".$key);
            }
        }

        $this->id = $id;
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
        $this->dbRegex = $dbRegex;
    }

    public function hasDatabase($database) {
        if (is_null($this->dbRegex)) {
            return true;
        } else {
            return (bool)preg_match('/^'.$this->dbRegex.'\z/', $database);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getHost() {
        return $this->host;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getCharset() {
        return $this->charset;
    }
}
