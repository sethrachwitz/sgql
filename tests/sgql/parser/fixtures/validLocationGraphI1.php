<?php

namespace SGQL;

$input = '`column1`,`schema1`:[s1col1 AS s1c1,COUNT(`schema2`) AS schema2count, SUM(`schema2`:`s2col1`) AS s2col1sum],`column2`,MAX(`test`:`col`) AS maxcol';

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
        Parser::TOKEN_LOCATION_GRAPH => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 's1col1',
                'withBackticks' => '`s1col1`',
                'location' => 21,
                Parser::TOKEN_ALIAS => [
                    'type' => Parser::TOKEN_ALIAS,
                    'value' => 's1c1',
                    'withBackticks' => '`s1c1`',
                    'location' => 28,
                ]
            ],
            [
                'type' => Parser::TOKEN_NAMESPACE_COUNT,
                Parser::TOKEN_COUNT_FUNCTION_NAME => [
                    'value' => 'COUNT',
                    'location' => 36,
                ],
                Parser::TOKEN_NAMESPACE => [
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'schema2',
                        'withBackticks' => '`schema2`',
                        'location' => 42,
                    ]
                ],
                Parser::TOKEN_ALIAS => [
                    'type' => Parser::TOKEN_ALIAS,
                    'value' => 'schema2count',
                    'withBackticks' => '`schema2count`',
                    'location' => 53,
                ],
            ],
            [
                'type' => Parser::TOKEN_LOCATION_AGGREGATION,
                Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
                    'value' => 'SUM',
                    'location' => 70,
                ],
                Parser::TOKEN_LOCATION => [
                    'type' => Parser::TOKEN_LOCATION,
                    Parser::TOKEN_NAMESPACE => [
                        [
                            'type' => Parser::TOKEN_ENTITY_NAME,
                            'value' => 'schema2',
                            'withBackticks' => '`schema2`',
                            'location' => 74,
                        ]
                    ],
                    Parser::TOKEN_ENTITY_NAME => [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 's2col1',
                        'withBackticks' => '`s2col1`',
                        'location' => 84,
                    ],
                ],
                Parser::TOKEN_ALIAS => [
                    'type' => Parser::TOKEN_ALIAS,
                    'value' => 's2col1sum',
                    'withBackticks' => '`s2col1sum`',
                    'location' => 94,
                ],
            ],
        ],
    ],
    [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'column2',
        'withBackticks' => '`column2`',
        'location' => 108,
    ],
    [
        'type' => Parser::TOKEN_LOCATION_AGGREGATION,
        Parser::TOKEN_AGGREGATION_FUNCTION_NAME => [
            'value' => 'MAX',
            'location' => 118,
        ],
        Parser::TOKEN_LOCATION => [
            'type' => Parser::TOKEN_LOCATION,
            Parser::TOKEN_NAMESPACE => [
                [
                    'type' => Parser::TOKEN_ENTITY_NAME,
                    'value' => 'test',
                    'withBackticks' => '`test`',
                    'location' => 122,
                ]
            ],
            Parser::TOKEN_ENTITY_NAME => [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'col',
                'withBackticks' => '`col`',
                'location' => 129,
            ],
        ],
        Parser::TOKEN_ALIAS => [
            'type' => Parser::TOKEN_ALIAS,
            'value' => 'maxcol',
            'withBackticks' => '`maxcol`',
            'location' => 136,
        ],
    ],
];
