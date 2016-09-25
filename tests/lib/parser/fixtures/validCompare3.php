<?php

$input = 'COUNT(`orders`) > 5';

$expected = [
    'type' => Parser::TOKEN_COMPARE,
    'has' => false,
    'key' => [
        'type' => Parser::TOKEN_NAMESPACE_COUNT,
        Parser::TOKEN_COUNT_FUNCTION_NAME => [
            'value' => 'COUNT',
            'location' => 0,
        ],
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 6,
            ],
        ],
    ],
    Parser::TOKEN_COMPARISON => [
        'type' => Parser::TOKEN_COMPARISON,
        'value' => '>',
        'location' => 16,
    ],
    Parser::TOKEN_VALUE => [
        'type' => Parser::TOKEN_INTEGER,
        'value' => '5',
        'location' => 18,
    ],
];
