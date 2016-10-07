<?php

namespace SGQL;

$input = '`cost` = ?param';

$expected = [
    'type' => Parser::TOKEN_ENTITY_ASSIGN,
    'key' => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'cost',
        'withBackticks' => '`cost`',
        'location' => 0,
    ],
    'value' => [
        'type' => Parser::TOKEN_PARAMETER,
        'value' => 'param',
        'location' => 9,
    ],
];
