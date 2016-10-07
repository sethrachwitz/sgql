<?php

namespace SGQL;

$input = '12,"string",`schema1`:[12.99,?param]';

$expected = [
    [
        'type' => Parser::TOKEN_INTEGER,
        'value' => '12',
        'location' => 0,
    ],
    [
        'type' => Parser::TOKEN_STRING,
        'value' => 'string',
        'withQuotes' => '"string"',
        'location' => 3,
    ],
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'schema1',
        'withBackticks' => '`schema1`',
        'location' => 12,
        Parser::TOKEN_VALUE_GRAPH => [
            [
                'type' => Parser::TOKEN_DOUBLE,
                'value' => '12.99',
                'location' => 23,
            ],
            [
                'type' => Parser::TOKEN_PARAMETER,
                'value' => 'param',
                'location' => 29,
            ],
        ],
    ],
];
