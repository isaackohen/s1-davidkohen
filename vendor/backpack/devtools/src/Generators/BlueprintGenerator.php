<?php

namespace Backpack\DevTools\Generators;

use Backpack\DevTools\GeneratorInterface;
use Backpack\DevTools\Models\Model;
use Backpack\DevTools\SchemaManager;
use Blueprint\Blueprint;
use Blueprint\Builder;
use Blueprint\Commands\TraceCommand;
use Blueprint\Models\Column;
use Blueprint\Translators\Rules;
use Doctrine\DBAL\Types\Types;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Prologue\Alerts\Facades\Alert;

class BlueprintGenerator implements GeneratorInterface
{
    use AlertConstructor;

    public $yamlFilePath;

    public $createdFiles;

    private $schema_manager;

    public function __construct()
    {
        $this->yamlFilePath = base_path('draft.yaml');
        $this->schema_manager = (new SchemaManager())->getManager();
    }

    public function generate($request)
    {
        $operation = Str::afterLast($request->path(), '/');

        switch ($operation) {
            case 'migration':
                return $this->generateMigration($request);
                break;
            case 'model':
                return $this->generateModel($request);
                break;
            default:
                abort(404, 'Unrecognized operation.');
        }
    }

    private function generateMigration($request)
    {
        $table = $request->input('table');
        $model = Str::of($table)->singular()->studly();

        // always generates migration
        $request->merge([
            'generate_migration' => true,
        ]);

        return $this->createAndRunYamlFile($request, $model);
    }

    private function generateModel($request)
    {
        $model = $request->input('name');
        $model = Str::of($model)->studly();

        // always generates model
        // generates migration if user wants to run the migration
        $request->merge([
            'generate_migration' => $request->boolean('run_migration'),
            'generate_model' => true,
        ]);

        return $this->createAndRunYamlFile($request, $model);
    }

    private function generateFilesWithBlueprint($only = '', $skip = '')
    {
        $blueprint = resolve(Blueprint::class);

        $builder = new Builder();
        $files = new Filesystem();

        return $builder->execute($blueprint, $files, $this->yamlFilePath, $only, $skip, false);
    }

    private function createAndRunYamlFile($request, $model)
    {
        $this->createYamlFile($model, $request);
        $alert = '';

        // if the database/seeders folder doesn't exist, but is needed
        // then go ahead and create it
        if ($request->boolean('generate_seeder') && ! file_exists(base_path('database/seeders'))) {
            mkdir(base_path('database/seeders'));
        }

        // when calling Artisan commands, run them from the base directory,
        // NOT the 'public' directory as they're run by default;
        // this is done so that files are generated in the right place,
        // since Blueprint uses relative paths
        Artisan::resolved(function () {
            chdir(base_path());
        });

        // before running blueprint, set the model folder
        Config::set('blueprint.models_namespace', 'Models');

        // generate resources to skip by the user input
        $skip = collect([
            'migrations' => ! $request->boolean('generate_migration'),
            'models' => ! $request->boolean('generate_model'),
            'factories' => ! $request->boolean('generate_factory'),
            'seeders' => ! $request->boolean('generate_seeder'),
        ])->filter()->keys()->join(',');

        // run blueprint on that file
        Artisan::call(TraceCommand::class);

        $output = $this->generateFilesWithBlueprint('', $skip);

        if (! isset($output['created'])) {
            abort(500, 'Something went wrong with Blueprint - the files might not have been created');
        }

        $this->composerDumpAutoload();

        $this->createdFiles = $this->getCreatedFileTypes($output['created']);

        if (! empty($this->createdFiles['migrations'])) {
            $alert .= $this->alertLine('Migration', 'generated');

            $this->registerFiles($this->createdFiles['migrations']);
        }

        // run migration if the user chose to
        if (! empty($this->createdFiles['migrations']) && $request->boolean('run_migration')) {
            foreach ($this->createdFiles['migrations'] as $migration) {
                Artisan::call('migrate', ['--path' => $migration, '--no-interaction' => true]);
            }

            $alert .= $this->alertLine('Migration', 'executed');
        }

        if (! empty($this->createdFiles['models'])) {
            $this->registerFiles($this->createdFiles['models']);

            $alert .= $this->alertLine('Model', 'generated');

            // boot up Sushi again, so that the new model shows up
            Model::clearBootedModels();

            foreach ($this->createdFiles['models'] as $generatedModel) {
                // blueprint auto-generates the name of the relation and we can't configure it.
                // we call this function to open the model file and replace the blueprint generated names
                // by the ones defined by developer in the interface.
                $this->changeRelationNameInModel($generatedModel, $request->get('relationships'));

                $this->removeFillableForMorphsColumns($generatedModel, $request->get('columns'));

                // get the model file generated by blueprint
                $item = Model::all()->first(function ($model) use ($generatedModel) {
                    // $generatedModel (by blueprint) always has unix slashes
                    // $model->file_path_relative may have unix or win slashes depending on the system
                    // that way by replacing windows '\' for unix '/' we ensure it always compare between same slashes
                    return Str::of($generatedModel)->contains(Str::of($model->file_path_relative)->replace('\\', '/'));
                });
            }

            $key = $item ? $item->getKey() : null;
        }

        if (! empty($this->createdFiles['factories'])) {
            $alert .= $this->alertLine('Factory', 'generated');
        }

        if (! empty($this->createdFiles['seeders'])) {
            $alert .= $this->alertLine('Seeder', 'generated');
        }

        // build the CRUD if the user chose to
        if (! empty($this->createdFiles['models']) && $request->boolean('build_crud')) {
            foreach ($this->createdFiles['models'] as $generatedModel) {
                $name = Str::of($generatedModel)->basename('.php');
                Artisan::call('backpack:crud', ['name' => $name]);

                // populate request
                $context = Str::of($model)->snake()->plural();

                $relationshipNameMap = collect($request->input('relationships'))
                    ->pluck('relationship_relation_name', 'relationship_column')
                    ->toArray();

                $columns = collect($request->input('columns'))
                    ->filter(function ($column) {
                        return isset($column['column_name']) && $column['column_type'] !== 'id';
                    })
                    ->map(function ($column) use ($context, $relationshipNameMap) {
                        $name = $column['column_name'];
                        $type = $column['column_type'];

                        $modifiers = collect($column['modifiers'] ?? [])
                            ->filter(function ($value, $key) {
                                return (bool) $value && $value !== 'false';
                            })
                            ->map(function ($value, $key) {
                                return in_array($key, ['nullable', 'unsigned', 'unique', 'index', 'useCurrent', 'useCurrentOnUpdate']) && ($value === '1' || $value === 'true' || $value === 'on') ? $key : $key;
                            })
                            ->toArray();

                        $values = Str::of(
                            collect($column)
                            ->except(['column_name', 'column_type', 'modifiers', 'show_modifiers'])
                            ->values()
                            ->join(',')
                        )
                            ->explode(',')
                            ->map(function ($name) {
                                return trim($name);
                            })
                            ->filter()
                            ->toArray() ?? [];

                        if (in_array($type, ['foreignId', 'belongsTo'])) {
                            $type = 'id';
                            $values = [$column['args']['foreign_table'] ?? (new $column['args']['model']())->getTable()];
                        }

                        $column = new Column($name, $type, $modifiers, $values);

                        $rules = Rules::fromColumn($context, $column);

                        if (in_array('nullable', $modifiers)) {
                            array_unshift($rules, 'nullable');
                        }

                        $rules = implode('|', $rules);

                        // replace field name with relationship name
                        if (array_key_exists($name, $relationshipNameMap)) {
                            $name = $relationshipNameMap[$name];
                        }

                        return "'$name' => '$rules',";
                    })
                    ->join("\n            ");

                $requestPath = app_path("Http/Requests/{$name}Request.php");
                if (File::exists($requestPath)) {
                    $result = Str::of(File::get($requestPath))->replaceFirst("// 'name' => 'required|min:5|max:255'", $columns);
                    File::put($requestPath, $result);
                }

                $alert .= $this->alertLine('CRUD Controller', 'generated');

                // improve crud controller relationship fields and columns
                $this->changeControllerRelationFields($request, $name);
            }
        }

        // delete the blueprint related files
        File::delete($this->yamlFilePath);
        File::delete(base_path('.blueprint'));

        Alert::success($alert)->flash();

        return $key ?? null;
    }

    private function registerFiles($files)
    {
        foreach ($files as $file) {
            require_once $file;
        }
    }

    public function createYamlFile($model, $request)
    {
        $columns = $request->input('columns', []);
        $relationships = collect($request->input('relationships', []))->filter(function ($item) {
            return ! isset($item['created_by_column']) || $item['created_by_column'] == false;
        });

        // generate the YAML file with our model info
        $content = collect();
        $content->push('models:');
        $content->push("  {$model}:");

        foreach ($columns as $column) {
            $name = $column['column_name'] ?? '';

            switch ($column['column_type']) {
                case 'belongsTo':
                    $type = 'foreignId';
                    break;
                case 'nullableTimestamps':
                    $type = 'timestamps';
                    break;
                default:
                    $type = $column['column_type'];
                    break;
            }

            $modifiers = collect($column['modifiers'] ?? [])
                ->except(['first', 'after']) // not supported
                ->filter(function ($value, $key) {
                    // if modifier is default, we allow whatever user input as we do the validation in the request
                    if (in_array($key, ['autoIncrement', 'unsigned', 'nullable', 'first', 'unique', 'index', 'useCurrent', 'useCurrentOnUpdate'])) {
                        // filter valid checkbox modifier values
                        if (is_string($value) && $value === 'false') {
                            return false;
                        }
                    }

                    return (bool) $value || $value === '0';
                })
                ->map(function ($value, $key) {
                    // if it's a chekbox (bool), it doesn't need the attribute
                    if (in_array($key, ['autoIncrement', 'unsigned', 'nullable', 'first', 'unique', 'index', 'useCurrent', 'useCurrentOnUpdate'])) {
                        return $key;
                    } else {
                        return "$key:'$value'";
                    }
                })
                ->join(' ');

            // foreign
            if ($type === 'foreignId') {
                $relation_model = $column['args']['model'] ?? null;
                $relation_table = $column['args']['foreign_table'] ?? (! is_null($relation_model) ? (new $relation_model())->getTable() : '');
                $relation_column = $column['args']['foreign_column'] ?? 'id';

                if ($relation_table) {
                    $referenced_column = collect($this->schema_manager->listTableColumns($relation_table))
                        ->filter(function ($item) use ($relation_column) {
                            return $item->getName() === $relation_column;
                        })
                        ->first();
                    if ($referenced_column && $referenced_column->getType()->getName() !== 'bigint') {
                        $type = $referenced_column->getType()->getName();
                        $modifiers .= ($referenced_column->getUnsigned() ? ' unsigned' : '');

                        // Map DBal types
                        $dbalMapping = [
                                Types::BIGINT => 'bigInteger',
                                Types::SMALLINT => 'smallInteger',
                                Types::BLOB => 'binary',
                                Types::DATE_IMMUTABLE => 'date',
                                Types::DATETIME_IMMUTABLE => 'dateTime',
                                Types::DATETIMETZ_IMMUTABLE => 'dateTimeTz',
                                Types::TIME_IMMUTABLE => 'time',
                                Types::SIMPLE_ARRAY => 'array',
                            ];
                        if (array_key_exists($type, $dbalMapping)) {
                            $type = $dbalMapping[$type];
                        }
                        $relation_table = ":$relation_table.$relation_column";
                        $content->push(rtrim("    $name: $type foreign$relation_table $modifiers"));

                        continue;
                    }

                    $relation_table = ":$relation_table.$relation_column";
                    $content->push(rtrim("    $name: $relation_column foreign$relation_table $modifiers"));
                } else {
                    $content->push(rtrim("    $name: bigInteger unsigned $modifiers"));
                }

                continue;
            }

            // morph
            if (in_array($type, ['morphs', 'uuidMorphs', 'nullableMorphs', 'nullableUuidMorphs'])) {
                $morphable = $column['args']['morphable'] ?? '';
                $size = $column['args']['size'] ?? '';

                if ($size) {
                    $size = ",$size";
                }

                $content->push(rtrim("    $morphable: $type$size $modifiers"));
                continue;
            }

            // enum and set
            if (in_array($type, ['enum', 'set'])) {
                // Clean up the string
                $column['args']['values'] = Str::of($column['args']['values'])
                    ->explode(',')
                    ->map(function ($name) {
                        return trim($name);
                    })
                    ->filter()
                    ->join(',');
            }

            // rememberToken
            if ($type === 'rememberToken') {
                $size = $column['args']['size'] ?? '';

                if ($size) {
                    $size = ": $size";
                }

                $content->push(rtrim("    $type$size $modifiers"));
                continue;
            }

            // timestamps and softDeletes
            if (in_array($type, ['id', 'timestamps', 'timestampsTz', 'softDeletes', 'softDeletesTz'])) {
                $content->push(rtrim("    $type"));
                continue;
            }

            $args = collect($column['args'] ?? [])
                ->filter()
                ->join(',');

            // if there are args, prepend a colon
            if (strlen($args)) {
                $args = ":$args";
            }

            $content->push(rtrim("    $name: $type$args $modifiers"));
        }

        // disable timestamps
        $usesTimestamps = collect($columns)->reduce(function ($carry, $column) {
            return $carry || in_array($column['column_type'], ['timestamps', 'timestampsTz', 'nullableTimestamps']);
        }, false);

        if (! $usesTimestamps) {
            $content->push('    timestamps: false');
        }

        // relations
        if (! $relationships->isEmpty()) {
            $content->push('    relationships:');
            $content->push(collect($relationships)->groupBy('relationship_type')->map(function ($value, $type) {
                switch ($type) {
                    case 'MorphTo': {
                        $models = $value
                        ->pluck('relationship_column')
                        ->join(', ');
                    }
                    break;
                    default: {
                        $models = $value
                        ->pluck('relationship_model')
                        ->map(function ($item) {
                            return Str::startsWith($item, '\\') ? $item : '\\'.$item;
                        })
                        ->join(', ');
                    }
                }

                return "      $type: $models";
            }));
        }

        // seeders
        if ($request->boolean('generate_seeder')) {
            $content->push("seeders: {$model}".PHP_EOL);
        }

        // write the generated blueprint YAML file
        File::put($this->yamlFilePath, trim($content->flatten()->join(PHP_EOL)), true);
    }

    public function buildSeeder($entry)
    {
        return $this->buildExtras($entry, 'seeders');
    }

    public function buildFactory($entry)
    {
        return $this->buildExtras($entry, 'factories');
    }

    private function buildExtras($entry, $extras)
    {
        $name = $entry->name;
        $namespace = (string) Str::of($entry->class_namespace)->after('App')->ltrim('\\');
        $this->yamlFilePath = base_path('.blueprint');
        $alert = '';

        \Config::set('blueprint.models_namespace', $namespace);

        Artisan::resolved(function () {
            chdir(base_path());
        });

        // if the database/seeders folder doesn't exist, create it
        if ($extras === 'seeders' && ! file_exists(base_path('database/seeders'))) {
            mkdir(base_path('database/seeders'));
        }

        // Blueprint trace the model to generate its blueprint
        Artisan::call(TraceCommand::class, ['--path' => (array) $entry->file_directory]);

        // Clean up entries
        $result = Str::of(File::get($this->yamlFilePath))
            ->replace('`', '')
            ->explode("\n")
            ->filter(function ($line) use ($name, $namespace) {
                return $line === 'models:' || preg_match("/($namespace\\\\)?$name/", $line);
            })
            ->map(function ($line) use ($namespace) {
                return str_replace($namespace, '', $line);
            })
            ->push("seeders: $name")
            ->join("\n");

        File::put($this->yamlFilePath, $result);

        $output = $this->generateFilesWithBlueprint($extras);

        $this->createdFiles = $this->getCreatedFileTypes($output['created']);

        // Factories
        if (count($this->createdFiles['factories'])) {
            $this->registerFiles($this->createdFiles['factories']);

            $alert .= $this->alertTitle('Factory');

            foreach ($this->createdFiles['factories'] as $factory) {
                $alert .= $this->alertLine($factory);
                $content = Str::of(File::get($entry->file));

                // Clear blueprint base 'App' on vendor models
                if (! $entry->isInsideAppDirectory()) {
                    File::put($factory, Str::of(File::get($factory))->replace('App\\'.$entry->classPath, $entry->classPath));
                }

                // Use HasFactory Trait
                if (! $content->contains('Illuminate\Database\Eloquent\Factories\HasFactory')) {
                    $content = $content
                        ->replaceFirst('use', 'use Illuminate\Database\Eloquent\Factories\HasFactory;'.PHP_EOL.'use')
                        ->replaceFirst('{', '{'.PHP_EOL.'    use HasFactory;');
                }

                File::put($entry->file, $content);
            }
        }

        // Seeders
        if (count($this->createdFiles['seeders'])) {
            $this->registerFiles($this->createdFiles['seeders']);

            $alert .= $this->alertTitle('Seeder');

            foreach ($this->createdFiles['seeders'] as $seeder) {
                $alert .= $this->alertLine($seeder);

                // Clear blueprint base 'App' on vendor models
                if (! $entry->isInsideAppDirectory()) {
                    File::put($seeder, Str::of(File::get($seeder))->replace('App\\'.$entry->classPath, $entry->classPath));
                }
            }
        }

        // boot up Sushi
        Model::clearBootedModels();

        $this->composerDumpAutoload();

        File::delete($this->yamlFilePath);

        return $alert;
    }

    /**
     * Applies missing modifiers to a Migration
     * Blueprint doesn't generate first and after modifiers.
     */
    private function applyMigrationAlterModifiers($migration, $columns)
    {
        // open the migration file to get existing content
        $filePath = base_path($migration);
        $content = Str::of(file_get_contents($filePath));

        foreach ($columns as $column) {
            $name = $column['column_name'] ?? '';
            $type = $column['column_type'] ?? '';
            $first = $column['modifiers']['first'] ?? false;
            $after = $column['modifiers']['after'] ?? false;

            // first
            if ($first) {
                $match = $content->match("/(table->(?:$name\(\)|\w+\(\'$name\'(?:,\s[^\)]+)*\)))/");
                $content = $content->replaceFirst($match, "{$match}->first()");
            }

            // after
            if ($after) {
                $match = $content->match("/(table->(?:$name\(\)|\w+\(\'$name\'(?:,\s[^\)]+)*\)))/");
                $content = $content->replaceFirst($match, "{$match}->after('{$after}')");
            }
        }
        // write file
        File::put($filePath, $content);
    }

    private function removeFillableForMorphsColumns($model, $columns)
    {
        $morphs_columns = collect($columns)->filter(function ($column) {
            return in_array($column['column_type'], ['morphs', 'nullableMorphs', 'uuidMorphs', 'nullableUuidMorphs']);
        });

        if (empty($morphs_columns)) {
            return;
        }

        $model_file = base_path($model);
        $content = Str::of(file_get_contents($model_file))->replace("\r", '');

        foreach ($morphs_columns as $column) {
            $pattern = "/'{$column['args']['morphable']}',/";
            $replacement = '';
            $content = preg_replace($pattern, $replacement, $content);
        }

        // write file
        File::put($model_file, $content);
    }

    /**
     * Changes the relation name in the model to match the developer defined name.
     * Blueprint automatically generate names for the relations.
     */
    private function changeRelationNameInModel($model, $relations)
    {
        if (empty($relations)) {
            return;
        }

        // open the model file to get existing content
        $filePath = base_path($model);
        $content = Str::of(file_get_contents($filePath))->replace("\r", '');
        foreach ($relations as $relation) {
            if (! in_array($relation['relationship_type'], ['BelongsTo', 'MorphOne', 'MorphMany'])) {
                continue;
            }

            $relation_column = ! empty($relation['relationship_column']) ? $relation['relationship_column'] : $relation['relationship_relation_name'];
            $relation_name = $relation['relationship_relation_name'] ?? $relation['relationship_column'];
            $relation_model = $relation['relationship_model'] ?? '';
            $striped_relation_model = Str::of($relation_model)->afterLast('\\');

            switch ($relation['relationship_type']) {
                case 'BelongsTo': {

                    $blueprint_relation_name = Str::of($relation_column)->beforeLast('_id')->camel();

                    // replace the function name and
                    // we should not rely on laravel key convention and add the key to the relation
                    $pattern = "/public function {$blueprint_relation_name}..\n\s+{\n\s+return .this->belongsTo..App.Models.{$striped_relation_model}::class.;/";
                    $replacement = "public function {$relation_name}()\n    {\n        return \$this->belongsTo(\\{$relation_model}::class, '{$relation_column}');";

                    $content = preg_replace($pattern, $replacement, $content);
                }
                break;
                case 'MorphOne': {
                    $blueprint_relation_name = Str::lower($striped_relation_model);
                    $bluerint_generated_morphs = Str::of(Str::singular($striped_relation_model))->append('able')->lower();

                    $pattern = "/public function {$blueprint_relation_name}..\n\s+{\n\s+return .this->morphOne..App.Models.{$striped_relation_model}::class, '{$bluerint_generated_morphs}'.;/";
                    $replacement = "public function {$blueprint_relation_name}()\n    {\n        return \$this->morphOne(\\{$relation_model}::class, '{$relation_column}');";

                    $content = preg_replace($pattern, $replacement, $content);
                }
                break;
                case 'MorphMany': {
                    $blueprint_relation_name = Str::lower(Str::plural($striped_relation_model));
                    $bluerint_generated_morphs = Str::of(Str::singular($striped_relation_model))->append('able')->lower();

                    $pattern = "/public function {$blueprint_relation_name}..\n\s+{\n\s+return .this->morphMany..App.Models.{$striped_relation_model}::class, '{$bluerint_generated_morphs}'.;/";
                    $replacement = "public function {$blueprint_relation_name}()\n    {\n        return \$this->morphMany(\\{$relation_model}::class, '{$relation_column}');";

                    $content = preg_replace($pattern, $replacement, $content);

                }
                break;
            }
        }
        // write file
        File::put($filePath, $content);
    }

    /**
     * Changes the CRUD Controller fields and columns relative to relationships.
     */
    private function changeControllerRelationFields($request, $name)
    {
        // improve crud controller relational fields and columns
        $controllerPath = app_path("Http/Controllers/Admin/${name}CrudController.php");
        $relationships = $request->input('relationships');

        if (File::exists($controllerPath) && $relationships) {
            $controllerContent = Str::of(File::get($controllerPath));

            foreach ($request->input('relationships') as $relationship) {
                switch ($relationship['relationship_type']) {
                    case 'BelongsTo':
                        $column = $relationship['relationship_column'];
                        $name = $relationship['relationship_relation_name'];

                        $controllerContent = $controllerContent
                            ->replaceFirst("CRUD::field('$column')", "CRUD::field('$name')")
                            ->replaceFirst("CRUD::column('$column')", "CRUD::column('$name')");
                        break;
                }
            }

            File::put($controllerPath, $controllerContent);
        }
    }

    private function getCreatedFileTypes($files)
    {
        $structure = [
            'models' => [],
            'migrations' => [],
            'factories' => [],
            'seeders' => [],
        ];

        foreach ($files as $file) {
            if (Str::startsWith($file, 'database/migrations')) {
                $structure['migrations'][] = $file;
            }
            if (Str::startsWith($file, 'app/Models')) {
                $structure['models'][] = $file;
            }
            if (Str::startsWith($file, 'database/factories')) {
                $structure['factories'][] = $file;
            }
            if (Str::startsWith($file, 'database/seeders')) {
                $structure['seeders'][] = $file;
            }
        }

        return $structure;
    }

    private function composerDumpAutoload()
    {
        if (! app()->runningUnitTests()) {
            exec('composer dump-autoload');
            sleep(2);
        }
    }
}
