<?php

namespace SGQL;

$input = '`customers` <- orders';

$expected = [
	'parent' => [
		'type' => Parser::TOKEN_ENTITY_NAME,
		'value' => 'customers',
		'withBackticks' => '`customers`',
		'location' => 0,
	],
	Parser::TOKEN_ASSOC_TYPE => [
		'type' => Parser::TOKEN_ASSOC_TYPE,
		'value' => '<-',
		'location' => 12,
	],
	'child' => [
		'type' => Parser::TOKEN_ENTITY_NAME,
		'value' => 'orders',
		'withBackticks' => '`orders`',
		'location' => 15,
	],
];
