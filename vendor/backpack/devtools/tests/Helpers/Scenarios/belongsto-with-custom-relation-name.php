<?php

return [
    'table' => 'RelationsWithCustomNames',
    'generate_migration' => true,
    'run_migration' => false,
    'generate_model' => true,
    'build_crud' => true,
    'generate_seeder' => false,
    'generate_factory' => false,
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
        ],
        [
            'column_type' => 'timestamps',
        ],
        [
            'column_name' => 'created_by',
            'column_type' => 'belongsTo',
            'args' => [
                'model' => 'App\User',
                'foreign_table' => 'users',
                'foreign_column' => 'id',
            ],
        ],
        [
            'column_name' => 'updated_by',
            'column_type' => 'belongsTo',
            'args' => [
                'model' => 'App\User',
                'foreign_table' => 'users',
                'foreign_column' => 'id',
            ],
        ],
    ],
    'relationships' => [
        [
            'relationship_type' => 'BelongsTo',
            'relationship_model' => '\App\User',
            'relationship_column' => 'created_by',
            'relationship_relation_name' => 'creator',
            'created_by_column' => true,
        ],
        [
            'relationship_type' => 'BelongsTo',
            'relationship_model' => '\App\User',
            'relationship_column' => 'updated_by',
            'relationship_relation_name' => 'updator',
            'created_by_column' => true,
        ],
    ],
];
