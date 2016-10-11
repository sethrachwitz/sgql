<?php

namespace SGQL;

$input = '5 customers PAGE 2, 10 customers.orders';

$expected = [
    [
        'type' => Parser::TOKEN_SHOW_I,
        'records' => [
            'type' => Parser::TOKEN_POSITIVE_INTEGER,
            'value' => '5',
            'location' => 0,
        ],
        Parser::TOKEN_SCHEMA => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'customers',
            'withBackticks' => '`customers`',
            'location' => 2,
        ],
        'page' => [
            'type' => Parser::TOKEN_POSITIVE_INTEGER,
            'value' => '2',
            'location' => 17,
        ],
    ],
    [
        'type' => Parser::TOKEN_SHOW_I,
        'records' => [
            'type' => Parser::TOKEN_POSITIVE_INTEGER,
            'value' => '10',
            'location' => 20,
        ],
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'customers',
                'withBackticks' => '`customers`',
                'location' => 23,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 33,
            ],
        ],
    ],
];
