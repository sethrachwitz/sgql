<?php

$input = 'cost >= 19.4 AND HAS(`customers`:`id` IN ?ids) == true';

$expected = [
    [
        'type' => Parser::TOKEN_COMPARE,
        'key' => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'cost',
            'withBackticks' => '`cost`',
            'location' => 0
        ],
        Parser::TOKEN_COMPARISON => [
            'type' => Parser::TOKEN_COMPARISON,
            'value' => '>=',
            'location' => 5,
        ],
        Parser::TOKEN_VALUE => [
            'type' => Parser::TOKEN_DOUBLE,
            'value' => '19.4',
            'location' => 8,
        ],
    ],
    [
        'type' => Parser::TOKEN_COMPARE,
        'key' => [
            'type' => Parser::TOKEN_HAS_COMPARE,
            Parser::TOKEN_LOCATION => [
                'type' => Parser::TOKEN_LOCATION,
                Parser::TOKEN_NAMESPACE => [
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'customers',
                        'withBackticks' => '`customers`',
                        'location' => 21,
                    ],
                ],
                Parser::TOKEN_COLUMN => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'id',
                    'withBackticks' => '`id`',
                    'location' => 33,
                ],
            ],
            Parser::TOKEN_COMPARISON => [
                'type' => Parser::TOKEN_COMPARISON,
                'value' => 'IN',
                'location' => 38,
            ],
            Parser::TOKEN_VALUE => [
                'type' => Parser::TOKEN_PARAMETER,
                'value' => 'ids',
                'location' => 41
            ]
        ],
        Parser::TOKEN_COMPARISON => [
            'type' => Parser::TOKEN_COMPARISON,
            'value' => '==',
            'location' => 47,
        ],
        Parser::TOKEN_VALUE => [
            'type' => Parser::TOKEN_BOOLEAN,
            'value' => 'true',
            'location' => 50,
        ],
    ],
];
