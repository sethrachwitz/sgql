<?php

$input = 'SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost`,`shipped`],SUM(`orders`:`cost`)]';

$expected = [
    strtoupper(Parser::KEYWORD_SELECT) => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'customers',
        'withBackticks' => '`customers`',
        'location' => 7,
        Parser::TOKEN_LOCATION_GRAPH => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'id',
                'withBackticks' => '`id`',
                'location' => 20,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'name',
                'withBackticks' => '`name`',
                'location' => 25,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 32,
                Parser::TOKEN_LOCATION_GRAPH_I => [
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'id',
                        'withBackticks' => '`id`',
                        'location' => 42,
                    ],
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'cost',
                        'withBackticks' => '`cost`',
                        'location' => 47,
                    ],
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'shipped',
                        'withBackticks' => '`shipped`',
                        'location' => 54,
                    ],
                ],
            ],
            [
                'type' => Parser::TOKEN_LOCATION_AGGREGATION,
                Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
                    'value' => 'SUM',
                    'location' => 65,
                ],
                Parser::TOKEN_LOCATION => [
                    'type' => Parser::TOKEN_LOCATION,
                    Parser::TOKEN_NAMESPACE => [
                        [
                            'type' => Parser::TOKEN_ENTITY_NAME,
                            'value' => 'orders',
                            'withBackticks' => '`orders`',
                            'location' => 69,
                        ]
                    ],
                    Parser::TOKEN_COLUMN => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'cost',
                        'withBackticks' => '`cost`',
                        'location' => 78,
                    ],
                ],
            ],
        ],
    ],
];
