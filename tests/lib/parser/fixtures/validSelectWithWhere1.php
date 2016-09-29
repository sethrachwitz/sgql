<?php

$input = 'SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost`,`shipped`],SUM(`orders`:`cost`) AS totalcost] WHERE `customers`:(`name` == "Steven") AND `customers`.`orders`:(id == 123)';

$expected = [
    Parser::KEYWORD_SELECT => [
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
                Parser::TOKEN_LOCATION_GRAPH => [
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
                    Parser::TOKEN_ENTITY_NAME => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'cost',
                        'withBackticks' => '`cost`',
                        'location' => 78,
                    ],
                ],
                Parser::TOKEN_ALIAS => [
                    'type' => Parser::TOKEN_ALIAS,
                    'value' => 'totalcost',
                    'withBackticks' => '`totalcost`',
                    'location' => 86,
                ],
            ],
        ],
    ],
    Parser::KEYWORD_WHERE => [
        [
            'type' => Parser::TOKEN_WHERE_COMPARE,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'customers',
                    'withBackticks' => '`customers`',
                    'location' => 106,
                ],
            ],
            Parser::TOKEN_COMPARES => [
                [
                    'type' => Parser::TOKEN_COMPARE,
                    'key' => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'name',
                        'withBackticks' => '`name`',
                        'location' => 119,
                    ],
                    Parser::TOKEN_COMPARISON => [
                        'type' => Parser::TOKEN_COMPARISON,
                        'value' => '==',
                        'location' => 126,
                    ],
                    Parser::TOKEN_VALUE => [
                        'type' => Parser::TOKEN_STRING,
                        'value' => 'Steven',
                        'withQuotes' => '"Steven"',
                        'location' => 129,
                    ],
                ],
            ],
        ],
        [
            'type' => Parser::TOKEN_WHERE_COMPARE,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'customers',
                    'withBackticks' => '`customers`',
                    'location' => 143,
                ],
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'orders',
                    'withBackticks' => '`orders`',
                    'location' => 155,
                ],
            ],
            Parser::TOKEN_COMPARES => [
                [
                    'type' => Parser::TOKEN_COMPARE,
                    'key' => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'id',
                        'withBackticks' => '`id`',
                        'location' => 165,
                    ],
                    Parser::TOKEN_COMPARISON => [
                        'type' => Parser::TOKEN_COMPARISON,
                        'value' => '==',
                        'location' => 168,
                    ],
                    Parser::TOKEN_VALUE => [
                        'type' => Parser::TOKEN_INTEGER,
                        'value' => '123',
                        'location' => 171,
                    ],
                ],
            ],
        ],
    ],
];
