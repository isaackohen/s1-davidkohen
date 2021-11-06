<?php

namespace Backpack\DevTools\Http\Livewire\Traits;

trait HasRelationTypes
{
    public $relation_types = [
        'BelongsTo' => [
            'available_column_types' => ['BelongsTo', 'ForeignId', 'Integer', 'BigInteger', 'SmallInteger', 'TinyInteger', 'MediumInteger', 'String', 'Text', 'LongText', 'UnsignedBigInteger', 'UnsignedTinyInteger', 'UnsignedSmallInteger', 'UnsignedMediumInteger'],
        ],
        'BelongsToMany' => [
            'configs' => ['table', 'foreignPivotKey', 'relatedPivotKey', 'parentKey', 'relatedKey', 'relation'],
        ],
        'HasMany' => [
            'configs' => ['foreignKey', 'localKey'],
        ],
        // TODO: MasManyThrough
        'HasOne' => [
            'configs' => ['foreignKey', 'localKey'],
        ],
        'MorphMany' => [
            'configs' => ['foreignKey', 'localKey'],
        ],
        'MorphOne' => [
            'configs' => ['foreignKey', 'localKey'],
        ],
        'MorphTo' => [
            'available_column_types' => ['morphs', 'nullableMorphs', 'uuidMorphs', 'nullableUuidMorphs'],
        ],
        // TODO: HasOneOrMany
        // TODO: HasOneThrough
        // TODO: MorphOneOrMany
        // TODO: MorphPivot
        // TODO: MorphToMany
    ];
}
