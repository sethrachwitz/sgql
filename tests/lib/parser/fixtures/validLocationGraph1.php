<?php

namespace SGQL;

$input = '`customers`:[`column1`,`schema1`:[s1col1,COUNT(`schema2`) AS schema2count, SUM(`schema2`:`s2col1`) AS s2col1sum], `column2`, MAX(`test`:`col`) AS maxcol]';

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
            Parser::TOKEN_LOCATION_GRAPH => [
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
                    Parser::TOKEN_ALIAS => [
                        'type' => Parser::TOKEN_ALIAS,
                        'value' => 'schema2count',
                        'withBackticks' => '`schema2count`',
                        'location' => 58,
                    ],
                ],
                [
                    'type' => Parser::TOKEN_LOCATION_AGGREGATION,
                    Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
                        'value' => 'SUM',
                        'location' => 75,
                    ],
                    Parser::TOKEN_LOCATION => [
                        'type' => Parser::TOKEN_LOCATION,
                        Parser::TOKEN_NAMESPACE => [
                            [
                                'type' => Parser::TOKEN_ENTITY_NAME,
                                'value' => 'schema2',
                                'withBackticks' => '`schema2`',
                                'location' => 79,
                            ]
                        ],
                        Parser::TOKEN_ENTITY_NAME => [
                            'type' => Parser::TOKEN_ENTITY_NAME,
                            'value' => 's2col1',
                            'withBackticks' => '`s2col1`',
                            'location' => 89,
                        ],
                    ],
                    Parser::TOKEN_ALIAS => [
                        'type' => Parser::TOKEN_ALIAS,
                        'value' => 's2col1sum',
                        'withBackticks' => '`s2col1sum`',
                        'location' => 99,
                    ],
                ],
            ],
        ],
        [
            'type' => Parser::TOKEN_ENTITY_NAME,
            'value' => 'column2',
            'withBackticks' => '`column2`',
            'location' => 114,
        ],
        [
            'type' => Parser::TOKEN_LOCATION_AGGREGATION,
            Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
                'value' => 'MAX',
                'location' => 125,
            ],
            Parser::TOKEN_LOCATION => [
                'type' => Parser::TOKEN_LOCATION,
                Parser::TOKEN_NAMESPACE => [
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'test',
                        'withBackticks' => '`test`',
                        'location' => 129,
                    ]
                ],
                Parser::TOKEN_ENTITY_NAME => [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'col',
                    'withBackticks' => '`col`',
                    'location' => 136,
                ],
            ],
            Parser::TOKEN_ALIAS => [
                'type' => Parser::TOKEN_ALIAS,
                'value' => 'maxcol',
                'withBackticks' => '`maxcol`',
                'location' => 143,
            ],
        ],
    ],
];
