<?php

namespace SGQL;

$input = 'cost = 19.4, shipped = true';

$expected = [
    [
        'type' => Parser::TOKEN_ENTITY_ASSIGN,
        'key' => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'cost',
            'withBackticks' => '`cost`',
            'location' => 0,
        ],
        'value' => [
            'type' => Parser::TOKEN_DOUBLE,
            'value' => '19.4',
            'location' => 7,
        ],
    ],
    [
        'type' => Parser::TOKEN_ENTITY_ASSIGN,
        'key' => [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'shipped',
            'withBackticks' => '`shipped`',
            'location' => 13,
        ],
        'value' => [
            'type' => Parser::TOKEN_BOOLEAN,
            'value' => 'true',
            'location' => 23,
        ],
    ],
];
