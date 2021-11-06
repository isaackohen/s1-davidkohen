<?php

namespace Backpack\DevTools\Http\Requests;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Schema;
use Str;

/**
 * Blueprint helper methods.
 */
trait BaseRequestTrait
{
    private $column_name_not_required = ['timestamps', 'timestampsTz', 'uuidMorphs', 'morphs', 'nullableTimestamps', 'nullableMorphs'];

    protected function commonRules()
    {
        return [
            'columns' => 'required|array|min:1',
            'columns.*.column_name' => function ($attribute, $value, $fail) {
                if (! in_array($value, $this->column_name_not_required) && empty($value)) {
                    $fail('The column name is required.');
                }
            },
            'columns.*.column_type' => 'required',
            'run_migration' => 'required|boolean',
            'generate_factory' => 'nullable|boolean',
            'generate_seeder' => 'nullable|boolean',
            'build_crud' => 'nullable|boolean',
        ];
    }

    // Check if table exists on sql db's only
    protected function validateTableExists($validator, $table)
    {
        $isSQL = in_array(Schema::getConnection()->getConfig('driver'), CRUD::getSqlDriverList());

        if ($isSQL && Schema::hasTable($table)) {
            return $validator->errors()->add('table', "The $table table already exists.");
        }
    }

    protected function validateModelExists($validator, $model)
    {
        if (class_exists("App\\Models\\$model")) {
            $validator->errors()->add('name', "The $model model already exists.");
        }
    }

    // Check if migration class exists
    protected function validateMigrationExists($validator, $table)
    {
        $name = Str::of($table)->plural()->studly();
        $migrationClass = "Create{$name}Table";

        if (class_exists($migrationClass)) {
            return $validator->errors()->add('run_migration', "Migration class '$migrationClass' already exists.");
        }
    }

    // Check there is only one id or bigIncrements
    protected function validateSingleIdOrBigIncrements($validator)
    {
        collect(request()->input('columns'))
            ->filter(function ($column) {
                return in_array($column['column_type'], ['id', 'bigIncrements']);
            })
            ->skip(1)
            ->each(function ($item, $key) use ($validator) {
                $validator->errors()->add("columns.$key.column_type", 'It can only exist one id or bigIncrements column.');
            });
    }

    // Check if value exists when it is required
    protected function validateValueRequired($validator)
    {
        $types = ['enum', 'set'];

        collect(request()->input('columns'))
            ->filter(function ($column) use ($types) {
                return in_array($column['column_type'], $types) && empty($column['args']['values']);
            })
            ->each(function ($column, $key) use ($validator) {
                $type = $column['column_type'];
                $validator->errors()->add("columns.$key.column_values", "Value is required for column type $type.");
            });
    }

    protected function validateRelationships($validator)
    {
        collect(request()->input('relationships'))
            ->filter(function ($relationship) {
                return $relationship['relationship_type'] === 'BelongsTo' && empty($relationship['relationship_column']);
            })
            ->each(function ($relationship, $key) use ($validator) {
                $validator->errors()->add("relationships.$key.relationship_column", 'BelongsTo relation needs an associated column.');
            });
    }

    protected function validateForeignIdColumns($validator)
    {
        collect(request()->input('columns'))
            ->filter(function ($column) {
                return in_array($column['column_type'], ['foreignId', 'belongsTo']);
            })
            ->each(function ($item, $key) use ($validator) {
                if (isset($item['table_no_index'])) {
                    $validator->errors()->add("columns.$key.column_type", 'No indexes in the related database table.');
                }

                switch ($item['column_type']) {
                    case 'foreignId':
                        {
                            if (empty($item['args']['foreign_table'])) {
                                $validator->errors()->add("columns.$key.column_type", 'Cant use foreignId without selecting a table.');
                            }
                        }
                        break;
                    case 'belongsTo':
                        {
                            if (empty($item['args']['model'])) {
                                $validator->errors()->add("columns.$key.column_type", 'Cant use BelongsTo without selecting a model.');
                            }
                        }
                        break;
                }
            });
    }

    protected function validateMorphsColumns($validator)
    {
        collect(request()->input('columns'))
            ->filter(function ($column) {
                return in_array($column['column_type'], ['morphs', 'nullableMorphs', 'uuidMorphs', 'nullableUuidMorphs']);
            })
            ->each(function ($item, $key) use ($validator) {
                if (empty($item['args']['morphable'])) {
                    $validator->errors()->add("columns.$key.args.morphable", 'Morphable is mandatory');
                }
            });
    }

    protected function validateNoColumnsWithSameName($validator)
    {
        $columns = collect(request()->input('columns'));
        $columns_unique = $columns->unique('column_name');
        foreach ($columns as $key => $column) {
            if (! $columns_unique->has($key) && ! in_array($column['column_type'], $this->column_name_not_required)) {
                $validator->errors()->add("columns.$key.column_name", 'Duplicated column name.');
            }
        }
    }

    protected function validateDefaultModifier($validator)
    {
        $types = [
            'integer' => ['tinyInteger', 'smallInteger', 'mediumInteger', 'integer', 'bigInteger', 'decimal', 'float', 'double', 'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger', 'unsignedInteger', 'unsignedBigInteger', 'unsignedDecimal', 'year'],
            'numeric' => ['decimal', 'float', 'double'],
            'date' => ['date', 'dateTime', 'dateTimeTz', 'timestampTz', 'timestamp'],
            'time' => ['time', 'timeTz'],
            'boolean' => ['boolean'],
        ];

        collect(request()->input('columns'))
            ->each(function ($item, $key) use ($validator, $types) {
                $type = $item['column_type'];
                $value = $item['modifiers']['default'] ?? null;
                $has_error = false;

                if (! $value) {
                    return;
                }

                // Integers
                if (in_array($type, $types['integer'])) {
                    $has_error = ! $validator->validateInteger("columns.$key.modifiers.default", $value);
                }

                // Numerics
                if (in_array($type, $types['numeric'])) {
                    $has_error = ! $validator->validateNumeric("columns.$key.modifiers.default", $value);
                }

                // Date
                if (in_array($type, $types['date'])) {
                    $has_error = ! $validator->validateDate("columns.$key.modifiers.default", $value);
                }

                // Time
                if (in_array($type, $types['time'])) {
                    $has_error = ! $validator->validateDateFormat("columns.$key.modifiers.default", $value, ['H:i:s']);
                }

                // Boolean
                if (in_array($type, $types['boolean'])) {
                    $has_error = ! in_array($value, ['true', 'false', '1', '0']);
                }

                if ($has_error) {
                    $validator->errors()->add("columns.$key.modifiers.default", "Invalid default value for column type $type");
                }
            });
    }
}
