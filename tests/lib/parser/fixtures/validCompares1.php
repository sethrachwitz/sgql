<?php

$input = 'cost >= 19.4 AND HAS `id` IN ?ids';

$expected = [
    [
        'type' => Parser::TOKEN_COMPARE,
        'has' => false,
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
        'has' => true,
        'key' => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'id',
            'withBackticks' => '`id`',
            'location' => 21,
        ],
        Parser::TOKEN_COMPARISON => [
            'type' => Parser::TOKEN_COMPARISON,
            'value' => 'IN',
            'location' => 26,
        ],
        Parser::TOKEN_VALUE => [
            'type' => Parser::TOKEN_PARAMETER,
            'value' => 'ids',
            'location' => 29,
        ],
    ],
];
