<?php

namespace SGQL;

$input = 'cost >= 19.4';

$expected = [
    'type' => Parser::TOKEN_COMPARE,
    'key' => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'cost',
        'withBackticks' => '`cost`',
        'location' => 0,
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
];
