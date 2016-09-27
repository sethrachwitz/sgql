<?php

$input = "INSERT `customers`:[name, company, orders:[cost, shipped]]
            VALUES `customers`:[\"Steve Jobs\", \"Apple\", `orders`:[?cost, false]]";

$expected = [
    Parser::KEYWORD_INSERT => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'customers',
        'withBackticks' => '`customers`',
        'location' => 7,
        Parser::TOKEN_LOCATION_GRAPH => [
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'name',
                'withBackticks' => '`name`',
                'location' => 20,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'company',
                'withBackticks' => '`company`',
                'location' => 26,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 35,
                Parser::TOKEN_LOCATION_GRAPH => [
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'cost',
                        'withBackticks' => '`cost`',
                        'location' => 43,
                    ],
                    [
                        'type' => Parser::TOKEN_ENTITY_NAME,
                        'value' => 'shipped',
                        'withBackticks' => '`shipped`',
                        'location' => 49,
                    ],
                ],
            ],
        ],
    ],
    Parser::KEYWORD_VALUES => [
        'type' => Parser::TOKEN_ENTITY_NAME,
        'value' => 'customers',
        'withBackticks' => '`customers`',
        'location' => 78,
        Parser::TOKEN_VALUE_GRAPH => [
            [
                'type' => Parser::TOKEN_STRING,
                'value' => 'Steve Jobs',
                'withQuotes' => '"Steve Jobs"',
                'location' => 91,
            ],
            [
                'type' => Parser::TOKEN_STRING,
                'value' => 'Apple',
                'withQuotes' => '"Apple"',
                'location' => 105,
            ],
            [
                'type' => Parser::TOKEN_ENTITY_NAME,
                'value' => 'orders',
                'withBackticks' => '`orders`',
                'location' => 114,
                Parser::TOKEN_VALUE_GRAPH => [
                    [
                        'type' => Parser::TOKEN_PARAMETER,
                        'value' => 'cost',
                        'location' => 124,
                    ],
                    [
                        'type' => Parser::TOKEN_BOOLEAN,
                        'value' => 'false',
                        'location' => 131,
                    ],
                ],
            ],
        ],
    ],
];
