<?php

namespace SGQL;

$input = '`orders`:`cost` > ?param';

$expected = [
    'type' => Parser::TOKEN_COLUMN_COMPARE,
    Parser::TOKEN_COLUMN => [
        'type' => Parser::TOKEN_COLUMN,
        Parser::TOKEN_SCHEMA => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'orders',
            'withBackticks' => '`orders`',
            'location' => 0,
        ],
        Parser::TOKEN_ENTITY_NAME => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'cost',
            'withBackticks' => '`cost`',
            'location' => 9,
        ],
    ],
    Parser::TOKEN_COMPARISON => [
        'type' => Parser::TOKEN_COMPARISON,
        'value' => '>',
        'location' => 16,
    ],
    Parser::TOKEN_VALUE => [
        'type' => Parser::TOKEN_PARAMETER,
        'value' => 'param',
        'location' => 18,
    ],
];
