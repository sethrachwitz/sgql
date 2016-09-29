<?php

$input = '`schema`:`column`';

$expected = [
    'type' => Parser::TOKEN_COLUMN,
    Parser::TOKEN_SCHEMA => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'schema',
        'withBackticks' => '`schema`',
        'location' => 0,
    ],
    Parser::TOKEN_ENTITY_NAME => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'column',
        'withBackticks' => '`column`',
        'location' => 9,
    ],
];
