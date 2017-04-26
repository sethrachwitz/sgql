<?php

namespace SGQL;

$input = 'CREATE ASSOCIATION `customers` <- orders';

$expected = [
	Parser::KEYWORD_CREATE => [
		Parser::KEYWORD_ASSOCIATION => [
			'parent' => [
				'type' => Parser::TOKEN_ENTITY_NAME,
				'value' => 'customers',
				'withBackticks' => '`customers`',
				'location' => 19,
			],
			Parser::TOKEN_ASSOC_TYPE => [
				'type' => Parser::TOKEN_ASSOC_TYPE,
				'value' => '<-',
				'location' => 31,
			],
			'child' => [
				'type' => Parser::TOKEN_ENTITY_NAME,
				'value' => 'orders',
				'withBackticks' => '`orders`',
				'location' => 34,
			],
		],
	],
];
