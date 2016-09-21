<?php

$input = '`schema1`.`schema2`.`schema3`';

$expected = [
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'schema1',
        'withBackticks' => '`schema1`',
        'location' => 0,
    ],
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'schema2',
        'withBackticks' => '`schema2`',
        'location' => 10,
    ],
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'schema3',
        'withBackticks' => '`schema3`',
        'location' => 20,
    ],
];
