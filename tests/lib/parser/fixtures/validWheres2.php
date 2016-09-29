<?php

$input = '`orders`:(cost >= 19.4 AND HAS(`customers`:`id` > 10) == true) AND `orders`.`customers`:(name == "Steve")`';

$expected = [
    [
        'type' => Parser::TOKEN_WHERE_COMPARE,
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 0,
            ],
        ],
        Parser::TOKEN_COMPARES => [
            [
                'type' => Parser::TOKEN_COMPARE,
                'key' => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'cost',
                    'withBackticks' => '`cost`',
                    'location' => 10,
                ],
                Parser::TOKEN_COMPARISON => [
                    'type' => Parser::TOKEN_COMPARISON,
                    'value' => '>=',
                    'location' => 15,
                ],
                Parser::TOKEN_VALUE => [
                    'type' => Parser::TOKEN_DOUBLE,
                    'value' => '19.4',
                    'location' => 18,
                ],
            ],
            [
                'type' => Parser::TOKEN_COMPARE,
                'key' => [
                    'type' => Parser::TOKEN_HAS_COMPARE,
                    Parser::TOKEN_LOCATION => [
                        'type' => Parser::TOKEN_LOCATION,
                        Parser::TOKEN_NAMESPACE => [
                            [
                                'type' => Parser::TOKEN_ENTITY_NAME,
                                'value' => 'customers',
                                'withBackticks' => '`customers`',
                                'location' => 31,
                            ],
                        ],
                        Parser::TOKEN_ENTITY_NAME => [
                            'type' => Parser::TOKEN_ENTITY_NAME,
                            'value' => 'id',
                            'withBackticks' => '`id`',
                            'location' => 43,
                        ],
                    ],
                    Parser::TOKEN_COMPARISON => [
                        'type' => Parser::TOKEN_COMPARISON,
                        'value' => '>',
                        'location' => 48,
                    ],
                    Parser::TOKEN_VALUE => [
                        'type' => Parser::TOKEN_INTEGER,
                        'value' => '10',
                        'location' => 50
                    ]
                ],
                Parser::TOKEN_COMPARISON => [
                    'type' => Parser::TOKEN_COMPARISON,
                    'value' => '==',
                    'location' => 54,
                ],
                Parser::TOKEN_VALUE => [
                    'type' => Parser::TOKEN_BOOLEAN,
                    'value' => 'true',
                    'location' => 57,
                ],
            ],
        ],
    ],
    [
        'type' => Parser::TOKEN_WHERE_COMPARE,
        Parser::TOKEN_NAMESPACE => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 67,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'customers',
                'withBackticks' => '`customers`',
                'location' => 76,
            ],
        ],
        Parser::TOKEN_COMPARES => [
            [
                'type' => Parser::TOKEN_COMPARE,
                'key' => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'name',
                    'withBackticks' => '`name`',
                    'location' => 89,
                ],
                Parser::TOKEN_COMPARISON => [
                    'type' => Parser::TOKEN_COMPARISON,
                    'value' => '==',
                    'location' => 94,
                ],
                Parser::TOKEN_VALUE => [
                    'type' => Parser::TOKEN_STRING,
                    'value' => 'Steve',
                    'withQuotes' => '"Steve"',
                    'location' => 97,
                ],
            ],
        ],
    ]
];
