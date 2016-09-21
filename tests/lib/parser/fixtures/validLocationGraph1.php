<?php

$input = '`customers`:[`column1`,`schema1`:[s1col1,COUNT(`schema2`),SUM(`schema2`:`s2col1`)],`column2`,MAX(`test`:`col`)]';

$expected = [
    'type' => Parser::TOKEN_ENTITY_NAME,
    'value' => 'customers',
    'withBackticks' => '`customers`',
    'location' => 0,
    Parser::TOKEN_LOCATION_GRAPH => [
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'column1',
            'withBackticks' => '`column1`',
            'location' => 13,
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'schema1',
            'withBackticks' => '`schema1`',
            'location' => 23,
            Parser::TOKEN_LOCATION_GRAPH_I => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 's1col1',
                    'withBackticks' => '`s1col1`',
                    'location' => 34,
                ],
                [
                    'type' => Parser::TOKEN_NAMESPACE_COUNT,
                    Parser::TOKEN_COUNT_FUNCTION_NAME => [
                        'value' => 'COUNT',
                        'location' => 41,
                    ],
                    Parser::TOKEN_NAMESPACE => [
                        [
                            'type' => Parser::TOKEN_ENTITY_NAME,
                            'value' => 'schema2',
                            'withBackticks' => '`schema2`',
                            'location' => 47,
                        ]
                    ],
                ],
                [
                    'type' => Parser::TOKEN_LOCATION_AGGREGATION,
                    Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
                        'value' => 'SUM',
                        'location' => 58,
                    ],
                    Parser::TOKEN_LOCATION => [
                        'type' => Parser::TOKEN_LOCATION,
                        Parser::TOKEN_NAMESPACE => [
                            [
                                'type' => Parser::TOKEN_ENTITY_NAME,
                                'value' => 'schema2',
                                'withBackticks' => '`schema2`',
                                'location' => 62,
                            ]
                        ],
                        Parser::TOKEN_COLUMN => [
                            'type' => Parser::TOKEN_ENTITY_NAME,
                            'value' => 's2col1',
                            'withBackticks' => '`s2col1`',
                            'location' => 72,
                        ],
                    ],
                ],
            ],
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'column2',
            'withBackticks' => '`column2`',
            'location' => 83,
        ],
        [
            'type' => Parser::TOKEN_LOCATION_AGGREGATION,
            Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
                'value' => 'MAX',
                'location' => 93,
            ],
            Parser::TOKEN_LOCATION => [
                'type' => Parser::TOKEN_LOCATION,
                Parser::TOKEN_NAMESPACE => [
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'test',
                        'withBackticks' => '`test`',
                        'location' => 97,
                    ]
                ],
                Parser::TOKEN_COLUMN => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'col',
                    'withBackticks' => '`col`',
                    'location' => 104,
                ],
            ],
        ],
    ],
];
