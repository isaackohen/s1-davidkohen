<?php

return [
    'name' => 'Modifier',
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
        ],
        // Nullable
        [
            'column_name' => 'nullable_string',
            'column_type' => 'string',
            'modifiers' => [
                'nullable' => '1',
            ],
        ],
        [
            'column_name' => 'nullable_integer',
            'column_type' => 'integer',
            'modifiers' => [
                'nullable' => '1',
            ],
        ],
        // Default
        [
            'column_name' => 'default_0',
            'column_type' => 'integer',
            'modifiers' => [
                'default' => '0',
            ],
        ],
        [
            'column_name' => 'default_100',
            'column_type' => 'integer',
            'modifiers' => [
                'default' => '100',
            ],
        ],
        [
            'column_name' => 'default_string',
            'column_type' => 'string',
            'modifiers' => [
                'default' => 'default value',
            ],
        ],
        // Comment
        [
            'column_name' => 'comment',
            'column_type' => 'string',
            'modifiers' => [
                'comment' => 'This is a comment',
            ],
        ],
        // Collation
        [
            'column_name' => 'collation',
            'column_type' => 'string',
            'modifiers' => [
                'collation' => 'utf8_general_ci',
            ],
        ],
        [
            'column_type' => 'timestamps',
        ],
    ],
];
