<?php

namespace SGQL;

$input = 'HAS(`orders`:`id` IN ?ids)';

$expected = [
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
        Parser::TOKEN_ENTITY_NAME => [
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
];
