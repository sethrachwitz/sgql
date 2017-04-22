<?php

namespace SGQL;

$input = '`customers`:["Steve Jobs",`orders`:[12.9 , false]],`customers`:["Larry Ellison",`orders`:[44, false]]';

$expected = [
	[
		'type' => Parser::TOKEN_ENTITY_NAME,
		'value' => 'customers',
		'withBackticks' => '`customers`',
		'location' => 0,
		Parser::TOKEN_VALUE_GRAPH => [
			[
				'type' => Parser::TOKEN_STRING,
				'value' => 'Steve Jobs',
				'withQuotes' => '"Steve Jobs"',
				'location' => 13,
			],
			[
				'type' => Parser::TOKEN_ENTITY_NAME,
				'value' => 'orders',
				'withBackticks' => '`orders`',
				'location' => 26,
				Parser::TOKEN_VALUE_GRAPH => [
					[
						'type' => Parser::TOKEN_DOUBLE,
						'value' => '12.9',
						'location' => 36,
					],
					[
						'type' => Parser::TOKEN_BOOLEAN,
						'value' => 'false',
						'location' => 43,
					],
				],
			],
		],
	],
	[
		'type' => Parser::TOKEN_ENTITY_NAME,
		'value' => 'customers',
		'withBackticks' => '`customers`',
		'location' => 51,
		Parser::TOKEN_VALUE_GRAPH => [
			[
				'type' => Parser::TOKEN_STRING,
				'value' => 'Larry Ellison',
				'withQuotes' => '"Larry Ellison"',
				'location' => 64,
			],
			[
				'type' => Parser::TOKEN_ENTITY_NAME,
				'value' => 'orders',
				'withBackticks' => '`orders`',
				'location' => 80,
				Parser::TOKEN_VALUE_GRAPH => [
					[
						'type' => Parser::TOKEN_INTEGER,
						'value' => '44',
						'location' => 90,
					],
					[
						'type' => Parser::TOKEN_BOOLEAN,
						'value' => 'false',
						'location' => 94,
					],
				],
			],
		],
	]
];
