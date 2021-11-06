<?php

namespace Backpack\DevTools\Http\Livewire;

use Livewire\Component;
use Str;

class RelationshipSchema extends Component
{
    use \Backpack\DevTools\Http\Livewire\Traits\InteractsWithApplication;
    use \Backpack\DevTools\Http\Livewire\Traits\HasRelationTypes;

    public $relationships = [];

    public $available_models_as_array;

    public $migration_schema_columns;

    protected $listeners = [
        'updateMigrationSchemaColumns',
        'removeRelationshipEvent',
        'createBelongsToRelation',
        'updateRelationColumnName',
        'updateRelationColumnIndex',
        'updateRelationshipModel',
        'unforceRelation',
        'updateRelationshipModelList',
        'createMorphToRelation',
    ];

    private $creating_model_relation = false;

    public $current_available_columns_for_belongs_to;

    public $current_available_columns_for_morphs;

    public function hydrate()
    {
        $this->interactsWithApplicationInitialization();
    }

    public function mount()
    {
        if (old('relationships')) {
            $this->relationships = old('relationships');
            if (session()->has('dvt_columns')) {
                $this->migration_schema_columns = session('dvt_columns');
                session()->forget('dvt_columns');
            }
        }

        foreach ($this->relationships as $index => $relation) {
            $this->relationships[$index]['created_by_column'] = ! isset($relation['relationship_column']) ? false : $this->isCreatedByColumn($relation['relationship_column']);
            $this->relationships[$index]['relationship_column_index'] = isset($relation['relationship_column']) ? $this->getcolumnIndexFromName($relation['relationship_column']) : false;
            $this->relationships[$index]['relationship_column'] = $relation['relationship_column'] ?? false;
            $this->relationships[$index]['relationship_relation_name'] = $relation['relationship_relation_name'] ?? $this->getRelationNameFromColumnName($relation);
        }
    }

    public function updatedRelationships($value, $updated_key)
    {
        $relationship_index = Str::beforeLast($updated_key, '.');
        $column_name = Str::afterLast($updated_key, '.');
        if ($column_name === 'relationship_column') {
            $this->relationships[$relationship_index]['relationship_column_index'] = $this->getcolumnIndexFromName($value);
            $this->relationships[$relationship_index]['relationship_relation_name'] = $this->getRelationNameFromColumnName($this->relationships[$relationship_index]['relationship_column']);
            $this->emitTo('migration-schema', 'updateRelationshipKeyByName', $value, $relationship_index);
        }

        if ($column_name === 'relationship_relation_name') {
            if ($this->relationships[$relationship_index]['relationship_type'] === 'MorphTo') {
                $this->emitTo('migration-schema', 'updateMorphableName', $value, $relationship_index);
            }
        }
    }

    public function selectRelationshipType($relation_index)
    {
    }

    public function modelChanged($relation_index)
    {
        $model = $this->relationships[$relation_index]['relationship_model'];
        $relation_type = $this->relationships[$relation_index]['relationship_type'];

        switch ($relation_type) {
            case 'MorphOne':
            case 'MorphMany': {
                $this->relationships[$relation_index]['relationship_relation_name'] = $this->inferMorphableFromModel($model);
            }
            break;
        }
    }

    public function addRelationship()
    {
        $column = ! empty($this->current_available_columns_for_belongs_to) ? $this->current_available_columns_for_belongs_to[array_key_first($this->current_available_columns_for_belongs_to)]['column_name'] : false;
        $column_index = ! empty($this->current_available_columns_for_belongs_to) ? $this->getColumnIndexFromName(array_shift($this->current_available_columns_for_belongs_to)['column_name']) : false;

        $this->relationships[] = [
            'relationship_model' => $this->models->first()['name'] ?? null,
            'relationship_type' => 'BelongsTo',
            'created_by_column' => false,
            'relationship_column' => $column,
            'relationship_column_index' => $column_index,
        ];

        if ($column) {
            $this->emitTo('migration-schema', 'updateRelationshipKey', $column_index, count($this->relationships) - 1);
        }
    }

    public function getcolumnIndexFromName($name)
    {
        return key(array_filter($this->migration_schema_columns, function ($item) use ($name) {
            return (isset($item['column_name']) && $item['column_name'] == $name) || (isset($item['args']['morphable']) && $item['args']['morphable'] === $name);
        }));
    }

    public function unforceRelation($relationship_index)
    {
        $this->relationships[$relationship_index]['created_by_column'] = false;
    }

    /**
     * Updates the relationship model of a connected migration column.
     *
     * @param string $model
     *
     * @return void
     */
    public function updateRelationshipModel(int $relationship_index, $model)
    {
        if (empty($model)) {
            $model = $this->getModelNameFromColumnName($this->relationships[$relationship_index]['relationship_column']);
        }
        $this->relationships[$relationship_index]['relationship_model'] = $model;
    }

    public function updateRelationColumnName(int $column_index, $value, $is_model_selected = false)
    {
        $relationship = array_filter($this->relationships, function ($relationship) use ($column_index) {
            return isset($relationship['relationship_column_index']) && $relationship['relationship_column_index'] == $column_index;
        });

        $this->relationships[key($relationship)]['relationship_column'] = $value;
        $this->relationships[key($relationship)]['relationship_relation_name'] = $this->getRelationNameFromColumnName($value);

        if (! $is_model_selected) {
            $this->relationships[key($relationship)]['relationship_model'] = $this->getModelNameFromColumnName($value);
        }
    }

    public function updateRelationshipModelList($created_model)
    {
        if (! empty($created_model) && (int) $this->creating_model_relation !== false) {
            $this->relationships[$this->creating_model_relation]['relationship_model'] = reset($created_model);
            $this->creating_model_relation = false;
        }
    }

    public function creatingModelRelationship($relation_index)
    {
        $this->creating_model_relation = $relation_index;
    }

    public function updateMigrationSchemaColumns($columns)
    {
        $this->migration_schema_columns = $columns;
    }

    public function updateRelationColumnIndex($relationship_index, $column_index)
    {
        $this->relationships[$relationship_index]['relationship_column_index'] = $column_index;
    }

    public function createBelongsToRelation($column, $column_index)
    {
        $relationship = array_filter($this->relationships, function ($item) use ($column_index) {
            return $item['relationship_column_index'] == $column_index;
        });

        if (! empty($relationship)) {
            $this->relationships[key($relationship)]['created_by_column'] = true;
            $this->relationships[key($relationship)]['relationship_model'] = $this->getModelNameFromColumnName($column['column_name']);
        } else {
            $relationship_model = $this->getModelNameFromColumnName($column['column_name']);
            $relationship_relation_name = $this->getRelationNameFromColumnName($column['column_name']);
            $this->relationships[] = [
                'relationship_model' => $relationship_model,
                'relationship_type' => 'BelongsTo',
                'relationship_column' => $column['column_name'],
                'relationship_column_index' => $column_index,
                'created_by_column' => true,
                'relationship_relation_name' => $relationship_relation_name,
            ];
            $this->emitTo('migration-schema', 'belongsToRelationCreated', $column_index, $relationship_model, count($this->relationships) - 1);
        }
    }

    public function createMorphToRelation($column_name, $column_index)
    {
        $this->relationships[] = [
            'relationship_type' => 'MorphTo',
            'relationship_column_index' => $column_index,
            'relationship_relation_name' => $column_name,
            'relationship_column' => $column_name,
            'created_by_column' => false,
        ];
        $this->emitTo('migration-schema', 'morphToRelationCreated', $column_index, count($this->relationships) - 1);
    }

    public function removeRelationshipAndColumn(int $relationship_index)
    {
        $relation_column = $this->relationships[$relationship_index]['relationship_column_index'];

        $this->removeRelationship($relationship_index);

        $this->emitTo('migration-schema', 'deleteRelationshipColumn', $relation_column);
    }

    public function removeRelationshipEvent(int $relationship_index)
    {
        $this->removeRelationship($relationship_index);
    }

    public function removeRelationship(int $relationship_index)
    {
        unset($this->relationships[$relationship_index]);
        //array values resets the array key to 0, 1, 2, 3 instead of 0, 1, 3 if you deleted a middle key.
        $this->relationships = array_values($this->relationships);

        $this->emitTo('migration-schema', 'relationshipRemoved', $relationship_index);
    }

    public function getColumnsAvailableForBelongsTo()
    {
        if (is_array($this->migration_schema_columns)) {
            $migration_columns = array_filter($this->migration_schema_columns, function ($item) {
                return isset($item['column_name']) && $item['column_name'] !== '' && in_array(strtolower($item['column_type']), array_map('strtolower', $this->relation_types['BelongsTo']['available_column_types']));
            });

            return $this->removePreviouslySelectedColumns($migration_columns);
        }

        return [];
    }

    public function getColumnsAvailableForMorphs()
    {
        if (is_array($this->migration_schema_columns)) {
            $migration_columns = array_filter($this->migration_schema_columns, function ($item) {
                return ! empty($item['args']['morphable']) && in_array(strtolower($item['column_type']), array_map('strtolower', $this->relation_types['MorphTo']['available_column_types']));
            });

            return $this->removePreviouslySelectedColumns($migration_columns);
        }

        return [];
    }

    public function removePreviouslySelectedColumns($columns)
    {
        $relations_with_columns = array_filter($this->relationships, function ($item) {
            return $item['relationship_column'] ?? false;
        });

        $column_names_to_exclude = array_map(function ($relation) {
            return $relation['relationship_column'];
        }, $relations_with_columns);

        return array_filter($columns, function ($column) use ($column_names_to_exclude) {
            switch ($column['column_type']) {
                case 'morphs':
                case 'nullableMorphs':
                case 'uuidMorphs':
                case 'nullableUuidMorphs': {
                    return ! in_array($column['args']['morphable'], $column_names_to_exclude);
                }
                break;
                default: {
                    return ! in_array($column['column_name'], $column_names_to_exclude);
                }
            }
        });
    }

    public function addNewRelationshipColumn($relationship_index)
    {
        switch ($this->relationships[$relationship_index]['relationship_type']) {
            case 'BelongsTo': {
                $column_name = Str::snake(Str::afterLast($this->relationships[$relationship_index]['relationship_model'], '\\')).'_id';
                $this->relationships[$relationship_index]['relationship_column'] = $column_name;
                $this->relationships[$relationship_index]['relationship_relation_name'] = $this->getRelationNameFromColumnName($column_name);

                $this->emitTo('migration-schema', 'addColumnForBelongsToRelation', $relationship_index, $column_name);
            }
            break;
            case 'MorphTo': {
                $this->relationships[$relationship_index]['relationship_relation_name'] = 'taggable';
                $this->relationships[$relationship_index]['relationship_column'] = 'taggable';

                $this->emitTo('migration-schema', 'addColumnForMorphToRelation', $relationship_index, 'taggable');
            }
            break;
        }
    }

    public function render()
    {
        $this->current_available_columns_for_belongs_to = $this->getColumnsAvailableForBelongsTo();
        $this->current_available_columns_for_morphs = $this->getColumnsAvailableForMorphs();

        return view('backpack.devtools::livewire.relationship-schema');
    }

    // this triggers a browser event that will ask for user confirmation.
    // after evaluating the user decision it runs or not the callback.
    public function confirmRelationshipDelete($callback, ...$argv)
    {
        $this->dispatchBrowserEvent('confirmRelationshipDelete', compact('callback', 'argv'));
    }
}
