<?php

$input = 'HAS `id` IN ?ids';

$expected = [
    'type' => Parser::TOKEN_COMPARE,
    'has' => true,
    'key' => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'id',
        'withBackticks' => '`id`',
        'location' => 4,
    ],
    Parser::TOKEN_COMPARISON => [
        'type' => Parser::TOKEN_COMPARISON,
        'value' => 'IN',
        'location' => 9,
    ],
    Parser::TOKEN_VALUE => [
        'type' => Parser::TOKEN_PARAMETER,
        'value' => 'ids',
        'location' => 12,
    ],
];
