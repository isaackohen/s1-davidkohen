<?php

return [
    'table' => 'something',
    'name' => 'Relationship',
    'generate_migration' => true,
    'run_migration' => true,
    'generate_model' => true,
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
        ],
        [
            'column_type' => 'timestamps',
        ],
    ],
    'relationships' => [
        [
            'relationship_type' => 'BelongsToMany',
            'relationship_model' => '\\App\\User',
        ],
    ],
];
