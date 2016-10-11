<?php

namespace SGQL;

$input = '10 customers.orders';

$expected = [
    'type' => Parser::TOKEN_SHOW_I,
    'records' => [
        'type' => Parser::TOKEN_POSITIVE_INTEGER,
        'value' => '10',
        'location' => 0,
    ],
    Parser::TOKEN_NAMESPACE => [
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'customers',
            'withBackticks' => '`customers`',
            'location' => 3,
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'orders',
            'withBackticks' => '`orders`',
            'location' => 13,
        ],
    ],
];
