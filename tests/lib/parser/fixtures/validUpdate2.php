<?php

$input = "UPDATE `customers` WHERE `customers`:(id == 4) SET vip = true ASSOCIATE `orders`:`id` == 1 DISASSOCIATE `orders`:`id` == 2";

$expected = [
    Parser::KEYWORD_UPDATE => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'customers',
        'withBackticks' => '`customers`',
        'location' => 7,
    ],
    Parser::KEYWORD_WHERE => [
        [
            'type' => Parser::TOKEN_WHERE_COMPARE,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'customers',
                    'withBackticks' => '`customers`',
                    'location' => 25,
                ],
            ],
            Parser::TOKEN_COMPARES => [
                [
                    'type' => Parser::TOKEN_COMPARE,
                    'key' => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'id',
                        'withBackticks' => '`id`',
                        'location' => 38,
                    ],
                    Parser::TOKEN_COMPARISON => [
                        'type' => Parser::TOKEN_COMPARISON,
                        'value' => '==',
                        'location' => 41,
                    ],
                    Parser::TOKEN_VALUE => [
                        'type' => Parser::TOKEN_INTEGER,
                        'value' => '4',
                        'location' => 44,
                    ],
                ],
            ],
        ],
    ],
    Parser::KEYWORD_SET => [
        [
            'type' => Parser::TOKEN_ENTITY_ASSIGN,
            'key' => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'vip',
                'withBackticks' => '`vip`',
                'location' => 51,
            ],
            'value' => [
                'type' => Parser::TOKEN_BOOLEAN,
                'value' => 'true',
                'location' => 57,
            ],
        ],
    ],
    Parser::KEYWORD_ASSOCIATE => [
        [
            'type' => Parser::TOKEN_COLUMN_COMPARE,
            Parser::TOKEN_COLUMN => [
                'type' => Parser::TOKEN_COLUMN,
                Parser::TOKEN_SCHEMA => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'orders',
                    'withBackticks' => '`orders`',
                    'location' => 72,
                ],
                Parser::TOKEN_ENTITY_NAME => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'id',
                    'withBackticks' => '`id`',
                    'location' => 81,
                ],
            ],
            Parser::TOKEN_COMPARISON => [
                'type' => Parser::TOKEN_COMPARISON,
                'value' => '==',
                'location' => 86,
            ],
            Parser::TOKEN_VALUE => [
                'type' => Parser::TOKEN_INTEGER,
                'value' => '1',
                'location' => 89,
            ],
        ],
    ],
    Parser::KEYWORD_DISASSOCIATE => [
        [
            'type' => Parser::TOKEN_COLUMN_COMPARE,
            Parser::TOKEN_COLUMN => [
                'type' => Parser::TOKEN_COLUMN,
                Parser::TOKEN_SCHEMA => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'orders',
                    'withBackticks' => '`orders`',
                    'location' => 104,
                ],
                Parser::TOKEN_ENTITY_NAME => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'id',
                    'withBackticks' => '`id`',
                    'location' => 113,
                ],
            ],
            Parser::TOKEN_COMPARISON => [
                'type' => Parser::TOKEN_COMPARISON,
                'value' => '==',
                'location' => 118,
            ],
            Parser::TOKEN_VALUE => [
                'type' => Parser::TOKEN_INTEGER,
                'value' => '2',
                'location' => 121,
            ],
        ],
    ],
];
