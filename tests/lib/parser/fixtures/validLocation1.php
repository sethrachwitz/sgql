<?php

$input = '`schema1`.`schema2`.`schema3`:`column`';

$expected = [
    'type' => Parser::TOKEN_LOCATION,
    Parser::TOKEN_NAMESPACE => [
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
    ],
    Parser::TOKEN_COLUMN => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'column',
        'withBackticks' => '`column`',
        'location' => 30,
    ],
];
