<?php

return [
    'name' => 'Relationship',
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
            'relationship_type' => 'BelongsTo',
            'relationship_model' => '\\App\\User',
        ],
        [
            'relationship_type' => 'BelongsToMany',
            'relationship_model' => '\\App\\User',
        ],
        [
            'relationship_type' => 'HasMany',
            'relationship_model' => '\\App\\User',
        ],
        [
            'relationship_type' => 'HasOne',
            'relationship_model' => '\\App\\User',
        ],
        [
            'relationship_type' => 'MorphMany',
            'relationship_model' => '\\App\\User',
        ],
        [
            'relationship_type' => 'MorphOne',
            'relationship_model' => '\\App\\User',
        ],
        [
            'relationship_type' => 'MorphTo',
            'relationship_model' => '\\App\\User',
        ],
    ],
];
