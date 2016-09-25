<?php

$input = '`orders`:(cost >= 19.4 AND `id` IN ?ids)';

$expected = [
    [
        'type' => Parser::TOKEN_WHERE_COMPARE,
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 0,
            ],
        ],
        Parser::TOKEN_COMPARES => [
            [
                'type' => Parser::TOKEN_COMPARE,
                'key' => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'cost',
                    'withBackticks' => '`cost`',
                    'location' => 10,
                ],
                Parser::TOKEN_COMPARISON => [
                    'type' => Parser::TOKEN_COMPARISON,
                    'value' => '>=',
                    'location' => 15,
                ],
                Parser::TOKEN_VALUE => [
                    'type' => Parser::TOKEN_DOUBLE,
                    'value' => '19.4',
                    'location' => 18,
                ],
            ],
            [
                'type' => Parser::TOKEN_COMPARE,
                'key' => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'id',
                    'withBackticks' => '`id`',
                    'location' => 27,
                ],
                Parser::TOKEN_COMPARISON => [
                    'type' => Parser::TOKEN_COMPARISON,
                    'value' => 'IN',
                    'location' => 32,
                ],
                Parser::TOKEN_VALUE => [
                    'type' => Parser::TOKEN_PARAMETER,
                    'value' => 'ids',
                    'location' => 35,
                ],
            ],
        ],
    ],
];
