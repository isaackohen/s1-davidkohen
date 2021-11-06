<?php

return [
    'name' => 'ColumnForeign',
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
        ],
        [
            'column_name' => 'user_id',
            'column_type' => 'foreignId',
            'args' => [
                'foreign_table' => 'users',
                'foreign_column' => 'id',
            ],
        ],
        [
            'column_name' => 'article_id',
            'column_type' => 'foreignId',
            'args' => [
                'foreign_table' => 'articles',
            ],
        ],
        [
            'column_name' => 'tag_id',
            'column_type' => 'foreignId',
            'args' => [
                'foreign_table' => 'tags',
            ],
        ],
        [
            'column_type' => 'timestamps',
        ],
    ],
];
