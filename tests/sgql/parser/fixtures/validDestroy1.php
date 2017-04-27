<?php

namespace SGQL;

$input = 'DESTROY ASSOCIATION `customers` <- orders';

$expected = [
	Parser::KEYWORD_DESTROY => [
		Parser::KEYWORD_ASSOCIATION => [
			'parent' => [
				'type' => Parser::TOKEN_ENTITY_NAME,
				'value' => 'customers',
				'withBackticks' => '`customers`',
				'location' => 20,
			],
			Parser::TOKEN_ASSOC_TYPE => [
				'type' => Parser::TOKEN_ASSOC_TYPE,
				'value' => '<-',
				'location' => 32,
			],
			'child' => [
				'type' => Parser::TOKEN_ENTITY_NAME,
				'value' => 'orders',
				'withBackticks' => '`orders`',
				'location' => 35,
			],
		],
	],
];
