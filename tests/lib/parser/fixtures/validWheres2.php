<?php

$input = '`orders`:(cost >= 19.4 AND HAS `id` IN ?ids) AND `orders`.`customers`:(HAS name == "Steve")`';

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
                'has' => false,
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
                'has' => true,
                'key' => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'id',
                    'withBackticks' => '`id`',
                    'location' => 31,
                ],
                Parser::TOKEN_COMPARISON => [
                    'type' => Parser::TOKEN_COMPARISON,
                    'value' => 'IN',
                    'location' => 36,
                ],
                Parser::TOKEN_VALUE => [
                    'type' => Parser::TOKEN_PARAMETER,
                    'value' => 'ids',
                    'location' => 39,
                ],
            ],
        ],
    ],
    [
        'type' => Parser::TOKEN_WHERE_COMPARE,
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 49,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'customers',
                'withBackticks' => '`customers`',
                'location' => 58,
            ],
        ],
        Parser::TOKEN_COMPARES => [
            [
                'type' => Parser::TOKEN_COMPARE,
                'has' => true,
                'key' => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'name',
                    'withBackticks' => '`name`',
                    'location' => 75,
                ],
                Parser::TOKEN_COMPARISON => [
                    'type' => Parser::TOKEN_COMPARISON,
                    'value' => '==',
                    'location' => 80,
                ],
                Parser::TOKEN_VALUE => [
                    'type' => Parser::TOKEN_STRING,
                    'value' => 'Steve',
                    'withQuotes' => '"Steve"',
                    'location' => 83,
                ],
            ],
        ],
    ]
];