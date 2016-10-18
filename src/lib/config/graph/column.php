<?php

namespace SGQL\Lib\Config;

class Column {
    private $name;
    private $details;

    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_DOUBLE = 'double';
    const TYPE_TEXT = 'text';

    const TYPES = [self::TYPE_BOOLEAN, self::TYPE_INTEGER, self::TYPE_DOUBLE, self::TYPE_TEXT];

    const INDEX_PRIMARY = 'primary';

    const INDEXES = [self::INDEX_PRIMARY];

    function __construct($name, $details) {
        $this->name = $name;
        $this->details = $details;

        if (!in_array($details['type'], self::TYPES)) {
            throw new \Exception("Invalid column type '".$details['type']."' for column '".$name."'");
        } else {
            if (isset($details['index']) && $details['type'] != self::TYPE_INTEGER && $details['index'] === self::INDEX_PRIMARY) {
                throw new \Exception("Primary columns can currently only have the integer type");
            }
        }
    }

    public function __get($attribute) {
        if ($this->hasAttribute($attribute)) {
            return $this->details[$attribute];
        } else {
            return null;
        }
    }

    public function hasAttribute($attribute) {
        return isset($this->details[$attribute]);
    }
}
