<?php

return [
    'table' => 'testings',
    'generate_migration' => true,
    'run_migration' => true,
    'generate_model' => true,
    'build_crud' => true,
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
            'modifiers' => [
                'charset' => null,
                'comment' => null,
                'collation' => null,
            ],
            'args' => [],
        ],
        [
            'column_type' => 'timestamps',
            'column_precision' => null,
            'modifiers' => [
                'default' => null,
                'charset' => null,
                'comment' => null,
                'collation' => null,
            ],
            'args' => [],
        ],
    ],
];
