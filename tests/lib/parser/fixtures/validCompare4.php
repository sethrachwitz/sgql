<?php

$input = 'SUM(`orders`:cost) > 200';

$expected = [
    'type' => Parser::TOKEN_COMPARE,
    'key' => [
        'type' => Parser::TOKEN_LOCATION_AGGREGATION,
        Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
            'value' => 'SUM',
            'location' => 0,
        ],
        Parser::TOKEN_LOCATION => [
            'type' => Parser::TOKEN_LOCATION,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'orders',
                    'withBackticks' => '`orders`',
                    'location' => 4,
                ]
            ],
            Parser::TOKEN_ENTITY_NAME => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'cost',
                'withBackticks' => '`cost`',
                'location' => 13
            ],
        ],
    ],
    Parser::TOKEN_COMPARISON => [
        'type' => Parser::TOKEN_COMPARISON,
        'value' => '>',
        'location' => 19,
    ],
    Parser::TOKEN_VALUE => [
        'type' => Parser::TOKEN_INTEGER,
        'value' => '200',
        'location' => 21,
    ],
];
