<?php

$input = '`schema1`';

$expected = [
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'schema1',
        'withBackticks' => '`schema1`',
        'location' => 0,
    ],
];
