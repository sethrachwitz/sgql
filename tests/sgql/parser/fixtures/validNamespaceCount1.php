<?php

namespace SGQL;

$input = 'COUNT(`schema1`.`schema2`.`schema3`)';

$expected = [
    'type' => Parser::TOKEN_NAMESPACE_COUNT,
    Parser::TOKEN_COUNT_FUNCTION_NAME => [
        'value' => 'COUNT',
        'location' => 0,
    ],
    Parser::TOKEN_NAMESPACE => [
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'schema1',
            'withBackticks' => '`schema1`',
            'location' => 6,
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'schema2',
            'withBackticks' => '`schema2`',
            'location' => 16,
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'schema3',
            'withBackticks' => '`schema3`',
            'location' => 26,
        ],
    ]
];
