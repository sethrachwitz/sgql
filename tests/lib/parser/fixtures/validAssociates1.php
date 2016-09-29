<?php

$input = 'orders:cost > 19.4, orders:shipped == true';

$expected = [
    [
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
                'location' => 7,
            ],
        ],
        Parser::TOKEN_COMPARISON => [
            'type' => Parser::TOKEN_COMPARISON,
            'value' => '>',
            'location' => 12,
        ],
        Parser::TOKEN_VALUE => [
            'type' => Parser::TOKEN_DOUBLE,
            'value' => '19.4',
            'location' => 14,
        ],
    ],
    [
        'type' => Parser::TOKEN_COLUMN_COMPARE,
        Parser::TOKEN_COLUMN => [
            'type' => Parser::TOKEN_COLUMN,
            Parser::TOKEN_SCHEMA => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 20,
            ],
            Parser::TOKEN_ENTITY_NAME => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'shipped',
                'withBackticks' => '`shipped`',
                'location' => 27,
            ],
        ],
        Parser::TOKEN_COMPARISON => [
            'type' => Parser::TOKEN_COMPARISON,
            'value' => '==',
            'location' => 35,
        ],
        Parser::TOKEN_VALUE => [
            'type' => Parser::TOKEN_BOOLEAN,
            'value' => 'true',
            'location' => 38,
        ],
    ],
];
