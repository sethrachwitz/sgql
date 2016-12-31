<?php

namespace SGQL\Lib\Graph;

class Association {
    const TYPE_ONE_TO_ONE = 0;
    const TYPE_MANY_TO_ONE = 1;
    const TYPE_MANY_TO_MANY = 2;

    const ASSOCIATION_TYPES = [self::TYPE_ONE_TO_ONE, self::TYPE_MANY_TO_ONE, self::TYPE_MANY_TO_MANY];

    private $schema1, $schema2;
    private $type;

    function __construct($schema1, $schema2, $type) {
        $this->schema1 = $schema1;
        $this->schema2 = $schema2;

        if (!in_array($type, self::ASSOCIATION_TYPES)) {
            throw new \Exception("Association between '".$schema1."' and '".$schema2."' has invalid type '".$type."'");
        }

        $this->type = $type;
    }
}
