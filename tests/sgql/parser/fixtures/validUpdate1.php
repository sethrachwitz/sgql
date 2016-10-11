<?php

namespace SGQL;

$input = "UPDATE `customers` WHERE `customers`:(id == 4) SET vip = true";

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
];
