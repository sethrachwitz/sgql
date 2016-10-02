<?php

$input = 'customers BY name ASC, customers.orders BY cost DESC';

$expected = [
    [
        'type' => Parser::TOKEN_ORDER_BY,
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'customers',
                'withBackticks' => '`customers`',
                'location' => 0,
            ],
        ],
        Parser::TOKEN_ENTITY_NAME => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'name',
            'withBackticks' => '`name`',
            'location' => 13,
        ],
        Parser::TOKEN_ORDER_DIRECTION => [
            'type' => Parser::TOKEN_ORDER_DIRECTION,
            'value' => 'ASC',
            'location' => 18,
        ],
    ],
    [
        'type' => Parser::TOKEN_ORDER_BY,
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
        Parser::TOKEN_ENTITY_NAME => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'cost',
            'withBackticks' => '`cost`',
            'location' => 43,
        ],
        Parser::TOKEN_ORDER_DIRECTION => [
            'type' => Parser::TOKEN_ORDER_DIRECTION,
            'value' => 'DESC',
            'location' => 48,
        ],
    ],
];
