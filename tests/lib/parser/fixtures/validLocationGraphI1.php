<?php

$input = '`column1`,`schema1`:[s1col1,COUNT(`schema2`),SUM(`schema2`:`s2col1`)],`column2`,MAX(`test`:`col`)';

$expected = [
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'column1',
        'withBackticks' => '`column1`',
        'location' => 0,
    ],
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'schema1',
        'withBackticks' => '`schema1`',
        'location' => 10,
        Parser::TOKEN_LOCATION_GRAPH_I => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 's1col1',
                'withBackticks' => '`s1col1`',
                'location' => 21,
            ],
            [
                'type' => Parser::TOKEN_NAMESPACE_COUNT,
                Parser::TOKEN_COUNT_FUNCTION_NAME => [
                    'value' => 'COUNT',
                    'location' => 28,
                ],
                Parser::TOKEN_NAMESPACE => [
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'schema2',
                        'withBackticks' => '`schema2`',
                        'location' => 34,
                    ]
                ],
            ],
            [
                'type' => Parser::TOKEN_LOCATION_AGGREGATION,
                Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
                    'value' => 'SUM',
                    'location' => 45,
                ],
                Parser::TOKEN_LOCATION => [
                    'type' => Parser::TOKEN_LOCATION,
                    Parser::TOKEN_NAMESPACE => [
                        [
                            'type' => Parser::TOKEN_ENTITY_NAME,
                            'value' => 'schema2',
                            'withBackticks' => '`schema2`',
                            'location' => 49,
                        ]
                    ],
                    Parser::TOKEN_COLUMN => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 's2col1',
                        'withBackticks' => '`s2col1`',
                        'location' => 59,
                    ],
                ],
            ],
        ],
    ],
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'column2',
        'withBackticks' => '`column2`',
        'location' => 70,
    ],
    [
        'type' => Parser::TOKEN_LOCATION_AGGREGATION,
        Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
            'value' => 'MAX',
            'location' => 80,
        ],
        Parser::TOKEN_LOCATION => [
            'type' => Parser::TOKEN_LOCATION,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'test',
                    'withBackticks' => '`test`',
                    'location' => 84,
                ]
            ],
            Parser::TOKEN_COLUMN => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'col',
                'withBackticks' => '`col`',
                'location' => 91,
            ],
        ],
    ],
];
