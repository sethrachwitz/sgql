<?php

$input = 'HAS(`orders`:`id` IN ?ids) == true';

$expected = [
    'type' => Parser::TOKEN_COMPARE,
    'key' => [
        'type' => Parser::TOKEN_HAS_COMPARE,
        Parser::TOKEN_LOCATION => [
            'type' => Parser::TOKEN_LOCATION,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'orders',
                    'withBackticks' => '`orders`',
                    'location' => 4,
                ],
            ],
            Parser::TOKEN_COLUMN => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'id',
                'withBackticks' => '`id`',
                'location' => 13,
            ],
        ],
        Parser::TOKEN_COMPARISON => [
            'type' => Parser::TOKEN_COMPARISON,
            'value' => 'IN',
            'location' => 18,
        ],
        Parser::TOKEN_VALUE => [
            'type' => Parser::TOKEN_PARAMETER,
            'value' => 'ids',
            'location' => 21
        ],
    ],
    Parser::TOKEN_COMPARISON => [
        'type' => Parser::TOKEN_COMPARISON,
        'value' => '==',
        'location' => 27,
    ],
    Parser::TOKEN_VALUE => [
        'type' => Parser::TOKEN_BOOLEAN,
        'value' => 'true',
        'location' => 30,
    ],
];
