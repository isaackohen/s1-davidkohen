<?php

return [
    'table' => 'giants',
    'generate_migration' => true,
    'run_migration' => true,
    'generate_model' => true,
    'build_crud' => true,
    'generate_seeder' => true,
    'generate_factory' => true,
    'columns' => [
        'id' => [
            'column_name' => 'id',
            'column_type' => 'id',
        ],
        'bigInteger' => [
            'column_name' => 'bigInteger',
            'column_type' => 'bigInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'binary' => [
            'column_name' => 'binary',
            'column_type' => 'binary',
            'modifiers' => [
                'nullable' => true,
                'default' => 0,
            ],
        ],
        'boolean' => [
            'column_name' => 'boolean',
            'column_type' => 'boolean',
            'modifiers' => [
                'nullable' => true,
                'default' => true,
            ],
        ],
        'char' => [
            'column_name' => 'char',
            'column_type' => 'char',
            'args' => [
                'size' => '1',
            ],
            'modifiers' => [
                'nullable' => true,
                'default' => 'A',
            ],
        ],
        'dateTimeTz' => [
            'column_name' => 'dateTimeTz',
            'column_type' => 'dateTimeTz',
            'args' => [
                'precision' => '0',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'dateTime' => [
            'column_name' => 'dateTime',
            'column_type' => 'dateTime',
            'args' => [
                'precision' => '0',
            ],
            'modifiers' => [
                'nullable' => true,
                'default' => '1970-01-01 00.00.00',
            ],
        ],
        'date' => [
            'column_name' => 'date',
            'column_type' => 'date',
            'modifiers' => [
                'nullable' => true,
                'default' => '1970-01-01',
            ],
        ],
        'decimal' => [
            'column_name' => 'decimal',
            'column_type' => 'decimal',
            'args' => [
                'precision' => '4',
                'scale' => '2',
            ],
            'modifiers' => [
                'nullable' => true,
                'default' => 0.5,
            ],
        ],
        'double' => [
            'column_name' => 'double',
            'column_type' => 'double',
            'args' => [
                'precision' => '4',
                'scale' => '2',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'enum' => [
            'column_name' => 'enum',
            'column_type' => 'enum',
            'args' => [
                'values' => 'first, second, third',
            ],
            'modifiers' => [
                'nullable' => true,
                'default' => 'first',
            ],
        ],
        'float' => [
            'column_name' => 'float',
            'column_type' => 'float',
            'args' => [
                'precision' => '4',
                'scale' => '2',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'foreignId' => [
            'column_name' => 'foreignId',
            'column_type' => 'foreignId',
            'args' => [
                'foreign_table' => 'users',
                'foreign_column' => 'id',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'geometryCollection' => [
            'column_name' => 'geometryCollection',
            'column_type' => 'geometryCollection',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'geometry' => [
            'column_name' => 'geometry',
            'column_type' => 'geometry',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'integer' => [
            'column_name' => 'integer',
            'column_type' => 'integer',
            'modifiers' => [
                'nullable' => true,
                'default' => 100,
            ],
        ],
        'ipAddress' => [
            'column_name' => 'ipAddress',
            'column_type' => 'ipAddress',
            'modifiers' => [
                'nullable' => true,
                'default' => '192.168.0.1',
            ],
        ],
        'json' => [
            'column_name' => 'json',
            'column_type' => 'json',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'jsonb' => [
            'column_name' => 'jsonb',
            'column_type' => 'jsonb',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'lineString' => [
            'column_name' => 'lineString',
            'column_type' => 'lineString',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'longText' => [
            'column_name' => 'longText',
            'column_type' => 'longText',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'macAddress' => [
            'column_name' => 'macAddress',
            'column_type' => 'macAddress',
            'modifiers' => [
                'nullable' => true,
                'default' => '00-00-00-00-00-00',
            ],
        ],
        'mediumInteger' => [
            'column_name' => 'mediumInteger',
            'column_type' => 'mediumInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'mediumText' => [
            'column_name' => 'mediumText',
            'column_type' => 'mediumText',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'morphs' => [
            'column_name' => 'morphs',
            'column_type' => 'morphs',
            'args' => [
                'morphable' => 'taggable',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'multiLineString' => [
            'column_name' => 'multiLineString',
            'column_type' => 'multiLineString',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'multiPoint' => [
            'column_name' => 'multiPoint',
            'column_type' => 'multiPoint',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'multiPolygon' => [
            'column_name' => 'multiPolygon',
            'column_type' => 'multiPolygon',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'point' => [
            'column_name' => 'point',
            'column_type' => 'point',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'polygon' => [
            'column_name' => 'polygon',
            'column_type' => 'polygon',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'rememberToken' => [
            'column_name' => 'rememberToken',
            'column_type' => 'rememberToken',
            'args' => [
                'size' => '100',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'smallInteger' => [
            'column_name' => 'smallInteger',
            'column_type' => 'smallInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'string' => [
            'column_name' => 'string',
            'column_type' => 'string',
            'args' => [
                'size' => '255',
            ],
            'modifiers' => [
                'nullable' => true,
                'default' => 'sample "text"',
            ],
        ],
        'text' => [
            'column_name' => 'text',
            'column_type' => 'text',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'timeTz' => [
            'column_name' => 'timeTz',
            'column_type' => 'timeTz',
            'args' => [
                'precision' => '0',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'time' => [
            'column_name' => 'time',
            'column_type' => 'time',
            'args' => [
                'precision' => '0',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'timestamp' => [
            'column_name' => 'timestamp',
            'column_type' => 'timestamp',
            'args' => [
                'precision' => '0',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'timestampTz' => [
            'column_name' => 'timestampTz',
            'column_type' => 'timestampTz',
            'args' => [
                'precision' => '0',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'tinyInteger' => [
            'column_name' => 'tinyInteger',
            'column_type' => 'tinyInteger',
            'modifiers' => [
                'nullable' => true,
                'default' => 0,
            ],
        ],
        'unsignedBigInteger' => [
            'column_name' => 'unsignedBigInteger',
            'column_type' => 'unsignedBigInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'unsignedDecimal' => [
            'column_name' => 'unsignedDecimal',
            'column_type' => 'unsignedDecimal',
            'args' => [
                'precision' => '4',
                'scale' => '2',
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'unsignedInteger' => [
            'column_name' => 'unsignedInteger',
            'column_type' => 'unsignedInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'unsignedMediumInteger' => [
            'column_name' => 'unsignedMediumInteger',
            'column_type' => 'unsignedMediumInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'unsignedSmallInteger' => [
            'column_name' => 'unsignedSmallInteger',
            'column_type' => 'unsignedSmallInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'unsignedTinyInteger' => [
            'column_name' => 'unsignedTinyInteger',
            'column_type' => 'unsignedTinyInteger',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'uuidMorphs' => [
            'column_name' => 'uuidMorphs',
            'column_type' => 'uuidMorphs',
            'args' => [
                'morphable' => 'taggable',
                'size' => 36,
            ],
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'uuid' => [
            'column_name' => 'uuid',
            'column_type' => 'uuid',
            'modifiers' => [
                'nullable' => true,
            ],
        ],
        'year' => [
            'column_name' => 'year',
            'column_type' => 'year',
            'modifiers' => [
                'nullable' => true,
                'default' => '1970',
            ],
        ],
        'timestamps' => [
            'column_type' => 'timestamps',
        ],
        'softDeletes' => [
            'column_type' => 'softDeletes',
        ],
    ],
    'relationships' => [
        [
            'relationship_type' => 'BelongsTo',
            'relationship_model' => '\\App\\User',
            'relationship_column' => 'user_id',
            'relationship_relation_name' => 'user',
        ],
        [
            'relationship_type' => 'BelongsToMany',
            'relationship_model' => '\\App\\Tag',
            'relationship_column' => 'tag_id',
            'relationship_relation_name' => 'tag',
        ],
    ],
];
