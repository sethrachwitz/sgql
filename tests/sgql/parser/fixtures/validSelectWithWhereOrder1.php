<?php

namespace SGQL;

$input = "SELECT `customers`:[`id`,`name`,`orders`:[`id`,`cost`,`shipped`],SUM(`orders`:`cost`) AS totalcost]
            WHERE `customers`:(`name` == \"Steven\") AND `customers`.`orders`:(id == 123)
            ORDER `customers` BY `name` ASC, `customers`.`orders` BY `cost` ASC";

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
                    'location' => 118,
                ],
            ],
            Parser::TOKEN_COMPARES => [
                [
                    'type' => Parser::TOKEN_COMPARE,
                    'key' => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'name',
                        'withBackticks' => '`name`',
                        'location' => 131,
                    ],
                    Parser::TOKEN_COMPARISON => [
                        'type' => Parser::TOKEN_COMPARISON,
                        'value' => '==',
                        'location' => 138,
                    ],
                    Parser::TOKEN_VALUE => [
                        'type' => Parser::TOKEN_STRING,
                        'value' => 'Steven',
                        'withQuotes' => '"Steven"',
                        'location' => 141,
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
                    'location' => 155,
                ],
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'orders',
                    'withBackticks' => '`orders`',
                    'location' => 167,
                ],
            ],
            Parser::TOKEN_COMPARES => [
                [
                    'type' => Parser::TOKEN_COMPARE,
                    'key' => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'id',
                        'withBackticks' => '`id`',
                        'location' => 177,
                    ],
                    Parser::TOKEN_COMPARISON => [
                        'type' => Parser::TOKEN_COMPARISON,
                        'value' => '==',
                        'location' => 180,
                    ],
                    Parser::TOKEN_VALUE => [
                        'type' => Parser::TOKEN_INTEGER,
                        'value' => '123',
                        'location' => 183,
                    ],
                ],
            ],
        ],
    ],
    Parser::KEYWORD_ORDER => [
        [
            'type' => Parser::TOKEN_ORDER_BY,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'customers',
                    'withBackticks' => '`customers`',
                    'location' => 206,
                ],
            ],
            Parser::TOKEN_ENTITY_NAME => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'name',
                'withBackticks' => '`name`',
                'location' => 221,
            ],
            Parser::TOKEN_ORDER_DIRECTION => [
                'type' => Parser::TOKEN_ORDER_DIRECTION,
                'value' => 'ASC',
                'location' => 228,
            ],
        ],
        [
            'type' => Parser::TOKEN_ORDER_BY,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'customers',
                    'withBackticks' => '`customers`',
                    'location' => 233,
                ],
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'orders',
                    'withBackticks' => '`orders`',
                    'location' => 245,
                ],
            ],
            Parser::TOKEN_ENTITY_NAME => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'cost',
                'withBackticks' => '`cost`',
                'location' => 257,
            ],
            Parser::TOKEN_ORDER_DIRECTION => [
                'type' => Parser::TOKEN_ORDER_DIRECTION,
                'value' => 'ASC',
                'location' => 264,
            ],
        ],
    ],
];
