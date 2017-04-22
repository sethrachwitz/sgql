<?php

namespace SGQL;

$input = "INSERT `customers`:[name, company, orders:[cost, shipped]]
			VALUES `customers`:[\"Steve Jobs\", \"Apple\", `orders`:[?cost, false]]
			ASSOCIATE `orders`:`id` == 6";

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
		[
			'type' => Parser::TOKEN_ENTITY_NAME,
			'value' => 'customers',
			'withBackticks' => '`customers`',
			'location' => 69,
			Parser::TOKEN_VALUE_GRAPH => [
				[
					'type' => Parser::TOKEN_STRING,
					'value' => 'Steve Jobs',
					'withQuotes' => '"Steve Jobs"',
					'location' => 82,
				],
				[
					'type' => Parser::TOKEN_STRING,
					'value' => 'Apple',
					'withQuotes' => '"Apple"',
					'location' => 96,
				],
				[
					'type' => Parser::TOKEN_ENTITY_NAME,
					'value' => 'orders',
					'withBackticks' => '`orders`',
					'location' => 105,
					Parser::TOKEN_VALUE_GRAPH => [
						[
							'type' => Parser::TOKEN_PARAMETER,
							'value' => 'cost',
							'location' => 115,
						],
						[
							'type' => Parser::TOKEN_BOOLEAN,
							'value' => 'false',
							'location' => 122,
						],
					],
				],
			],
		],
	],
	Parser::KEYWORD_ASSOCIATE => [
		[
			'type' => Parser::TOKEN_COLUMN_COMPARE,
			Parser::TOKEN_COLUMN => [
				'type' => Parser::TOKEN_COLUMN,
				Parser::TOKEN_SCHEMA => [
					'type' => Parser::TOKEN_ENTITY_NAME,
					'value' => 'orders',
					'withBackticks' => '`orders`',
					'location' => 143,
				],
				Parser::TOKEN_ENTITY_NAME => [
					'type' => Parser::TOKEN_ENTITY_NAME,
					'value' => 'id',
					'withBackticks' => '`id`',
					'location' => 152,
				],
			],
			Parser::TOKEN_COMPARISON => [
				'type' => Parser::TOKEN_COMPARISON,
				'value' => '==',
				'location' => 157,
			],
			Parser::TOKEN_VALUE => [
				'type' => Parser::TOKEN_INTEGER,
				'value' => '6',
				'location' => 160,
			],
		],
	],
];
