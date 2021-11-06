<?php

return [
    'name' => 'Testing',
    'generate_migration' => false,
    'run_migration' => false,
    'generate_model' => true,
    'build_crud' => true,
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
            'modifiers' => [
                'default' => null,
                'charset' => null,
                'comment' => null,
                'collation' => null,
            ],
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
        ],
    ],
];
