<?php

namespace SGQL;

$input = 'MIN(`schema1`:`col1`)';

$expected = [
    'type' => Parser::TOKEN_LOCATION_AGGREGATION,
    Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
        'value' => 'MIN',
        'location' => 0,
    ],
    Parser::TOKEN_LOCATION => [
        'type' => Parser::TOKEN_LOCATION,
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'schema1',
                'withBackticks' => '`schema1`',
                'location' => 4,
            ],
        ],
        Parser::TOKEN_ENTITY_NAME => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'col1',
            'withBackticks' => '`col1`',
            'location' => 14,
        ],
    ]
];
