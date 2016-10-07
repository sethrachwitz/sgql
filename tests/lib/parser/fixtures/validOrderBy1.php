<?php

namespace SGQL;

$input = 'customers.orders BY cost DESC';

$expected = [
    'type' => Parser::TOKEN_ORDER_BY,
    Parser::TOKEN_NAMESPACE => [
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'customers',
            'withBackticks' => '`customers`',
            'location' => 0,
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'orders',
            'withBackticks' => '`orders`',
            'location' => 10,
        ],
    ],
    Parser::TOKEN_ENTITY_NAME => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'cost',
        'withBackticks' => '`cost`',
        'location' => 20,
    ],
    Parser::TOKEN_ORDER_DIRECTION => [
        'type' => Parser::TOKEN_ORDER_DIRECTION,
        'value' => 'DESC',
        'location' => 25,
    ],
];
