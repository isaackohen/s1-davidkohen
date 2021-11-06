<?php

return [
    'name' => 'Floats',
    'columns' => [
        [
            'column_name' => 'id',
            'column_type' => 'id',
        ],
        [
            'column_name' => 'float_with_args',
            'column_type' => 'float',
            'args' => [
                'precision' => 2,
                'scale' => 8,
            ],
        ],
        [
            'column_name' => 'float_without_args',
            'column_type' => 'float',
            'args' => [],
        ],
    ],
];
