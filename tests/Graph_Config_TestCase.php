<?php

namespace SGQL\Lib\Config;

class Graph_Config_TestCase extends \PHPUnit_Framework_TestCase {
    protected $customersSchema = [
        'id' => [
            'type' => 'integer',
            'index' => 'primary'
        ],
        'name' => [
            'type' => 'text',
        ],
        'vip' => [
            'type' => 'boolean',
            'default' => false,
        ],
    ];

    protected $graph = [
        'schemas' => [
            'customers' => [
                'id' => [
                    'type' => 'integer',
                    'index' => 'primary'
                ],
                'name' => [
                    'type' => 'text',
                ],
                'vip' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
            'orders' => [
                'id' => [
                    'type' => 'integer',
                    'index' => 'primary',
                ],
                'cost' => [
                    'type' => 'double',
                ],
                'shipped' => [
                    'type' => 'boolean',
                    'default' => false,
                ],
            ],
            'products' => [
                'id' => [
                    'type' => 'integer',
                    'index' => 'primary',
                ],
                'name' => [
                    'type' => 'text',
                ],
                'price' => [
                    'type' => 'double',
                ],
            ],
        ],
        'relationships' => [
            [
                'parent' => 'customers',
                'child' => 'orders',
                'type' => '<-',
            ],
            [
                'parent' => 'orders',
                'child' => 'products',
                'type' => '<->',
            ],
        ],
        'mode' => 'closed',
    ];
}
