<?php

return [
    'name' => 'ColumnEnum',
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
        ],
        [
            'column_name' => 'options_a',
            'column_type' => 'enum',
            'args' => [
                'values' => 'a,b,c',
            ],
        ],
        [
            'column_name' => 'options_b',
            'column_type' => 'enum',
            'args' => [
                'values' => 'a, b, c',
            ],
        ],
        [
            'column_name' => 'options_c',
            'column_type' => 'enum',
            'args' => [
                'values' => 'a, ,b,c',
            ],
        ],
        [
            'column_name' => 'options_d',
            'column_type' => 'set',
            'args' => [
                'values' => 'a,b,c',
            ],
        ],
        [
            'column_type' => 'timestamps',
        ],
    ],
];
