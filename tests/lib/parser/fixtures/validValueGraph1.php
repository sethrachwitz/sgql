<?php

$input = '`customers`:["Steve Jobs",`orders`:[12.9 , false]]';

$expected = [
    'type' => Parser::TOKEN_ENTITY_NAME,
    'value' => 'customers',
    'withBackticks' => '`customers`',
    'location' => 0,
    Parser::TOKEN_VALUE_GRAPH => [
        [
            'type' => Parser::TOKEN_STRING,
            'value' => 'Steve Jobs',
            'withQuotes' => '"Steve Jobs"',
            'location' => 13,
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'orders',
            'withBackticks' => '`orders`',
            'location' => 26,
            Parser::TOKEN_VALUE_GRAPH => [
                [
                    'type' => Parser::TOKEN_DOUBLE,
                    'value' => '12.9',
                    'location' => 36,
                ],
                [
                    'type' => Parser::TOKEN_BOOLEAN,
                    'value' => 'false',
                    'location' => 43,
                ],
            ],
        ],
    ],
];
