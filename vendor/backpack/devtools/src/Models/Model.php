<?php

namespace Backpack\DevTools\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\DevTools\CustomFile;
use File;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Schema;
use Sushi\Sushi;

class Model extends EloquentModel
{
    use CrudTrait;
    use Sushi;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'models';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = [
        'file_created_at',
        'file_last_accessed_at',
        'file_last_changed_at',
        'file_last_modified_at',
    ];

    protected $appends = [
        // 'crud_trait',
        // 'crud_controller',
        // 'requests',
        // 'table',
        // 'sidebar_item',
        // 'seeder',
        // 'factory',
        // 'migration',
        // 'content',
    ];

    public $incrementing = false;

    public $timestamps = true;

    public $sushiInsertChunkSize = 40;

    /*
    |--------------------------------------------------------------------------
    | SUSHI-SPECIFIC METHODS
    |--------------------------------------------------------------------------
    */

    public static function setSushiConnection($connection)
    {
        static::$sushiConnection = $connection;
    }

    /**
     * Instead of looking inside the database for results, this method is called
     * by Sushi to provide all rows for this Eloquent model (the Model model).
     *
     * @return array
     */
    public function getRows()
    {
        return CustomFile::allFrom()
            ->filter(function ($file) {
                return $file->isClass() && $file->isModel();
            })
            ->values()
            ->toArray();
    }

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the database table exists.
     *
     * @return bool
     */
    public function tableExists()
    {
        return Schema::hasTable($this->getTableAttribute());
    }

    public function getRelatedFiles()
    {
        return [
            'model' => $this->file_path ? new CustomFile($this->file_path) : '',
            'migration' => $this->migration_path ? new CustomFile($this->migration_path) : '',
            'seeder' => $this->seeder_path ? new CustomFile($this->seeder_path) : '',
            'factory' => $this->factory_path ? new CustomFile($this->factory_path) : '',
            'crud_controller' => $this->crud_controller_path ? new CustomFile($this->crud_controller_path) : '',
            'crud_requests' => $this->crud_request_files,
            'crud_route' => '',
            'sidebar_item' => '',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if the model file uses the CrudTrait inside.
     *
     * @return bool
     */
    public function getHasCrudTraitAttribute()
    {
        $content = Str::of($this->file_contents);

        // cover the case where the trait has its namespace imported
        if ($content->contains('use Backpack\CRUD\app\Models\Traits\CrudTrait') && $content->contains('use CrudTrait')) {
            return true;
        }

        // cover the case where the trait does NOT have its namespace imported
        if ($content->contains('use \Backpack\CRUD\app\Models\Traits\CrudTrait')) {
            return true;
        }

        return false;
    }

    /**
     * Check if the CRUD Trait can be added.
     *
     * @return bool
     */
    public function getCanAddCrudTraitAttribute()
    {
        return ! $this->hasCrudTrait && ! Str::startsWith($this->file_path_from_base, 'vendor');
    }

    /**
     * Check if the CRUD can be run.
     *
     * @return bool
     */
    public function getCanGenerateCrudAttribute()
    {
        return ! $this->crud_controller && $this->isInsideAppDirectory();
    }

    /**
     * Check if model can run seed operation.
     *
     * @return bool
     */
    public function getCanSeedAttribute()
    {
        return $this->hasFactory && $this->factory->isValid();
    }

    /**
     * Check if the model uses HasFactory.
     *
     * @return bool
     */
    public function getHasFactoryAttribute()
    {
        $content = Str::of($this->file_contents);

        return $content->contains('Illuminate\Database\Eloquent\Factories\HasFactory') && $content->match('/use (.*)HasFactory/') && $this->factory;
    }

    /**
     * Check if the Factory can be generated.
     *
     * @return bool
     */
    public function getCanGenerateFactoryAttribute()
    {
        return ! $this->hasFactory;
    }

    /**
     * Check if the model has a Seeder.
     *
     * @return bool
     */
    public function getHasSeederAttribute()
    {
        return (bool) $this->seeder;
    }

    /**
     * Check if the Seeder can be generated.
     *
     * @return bool
     */
    public function getCanGenerateSeederAttribute()
    {
        return ! $this->hasSeeder;
    }

    /**
     * Check if the model has a CRUD Controller.
     *
     * @return bool
     */
    public function getHasCrudControllerAttribute()
    {
        return (bool) $this->crudController;
    }

    /**
     * Find the Model CrudController.
     *
     * @return bool
     */
    public function getCrudControllerAttribute()
    {
        $classWithNamespace = $this->class_path;
        $className = $this->file_name;

        $paths = config('backpack.devtools.paths.crud_controllers');

        $file = CustomFile::allFrom($paths)
            ->filter(function ($file) {
                return $file->isClass() && $file->isCrudController();
            })
            ->filter(function ($file) use ($classWithNamespace, $className) {
                $possibleStrings = [
                    "setModel('".$classWithNamespace,
                    'setModel("'.$classWithNamespace,
                    'setModel(\\'.$classWithNamespace,
                    'setModel('.$className.'::class',
                ];

                return Str::contains($file->file_contents, $possibleStrings);
            })
            ->first();

        return $file ?? false;
    }

    /**
     * Model CrudController absolute path.
     *
     * @return string
     */
    public function getCrudControllerPathAttribute()
    {
        if ($this->crud_controller) {
            return $this->crud_controller->file_path_absolute;
        }
    }

    /**
     * Find the Model CrudRequest.
     *
     * @return bool
     */
    public function getCrudRequestsAttribute()
    {
        if (! $this->crud_controller_path) {
            return false;
        }

        $crudController = new CustomFile($this->crud_controller_path);
        $requests = [];

        // get the lines from the controller that contain a FormRequest
        foreach (explode("\n", $crudController->file_contents) as $key => $line) {
            if (Str::of($line)->contains(['CRUD::setValidation(', '$this->crud->setValidation'])) {
                $requests[] = Str::of($line)
                                    ->trim()
                                    ->replace('CRUD::setValidation(', '')
                                    ->replace('$this->crud->setValidation(', '')
                                    ->replaceLast('::class);', '')
                                    ->replace('\'', '')
                                    ->replace('"', '');
            }
        }

        $files = CustomFile::allFrom(config('backpack.devtools.paths.crud_requests'))
            ->filter(function ($file) {
                return $file->isClass() && $file->isFormRequest();
            })
            ->filter(function ($file) use ($requests) {
                $possibleStrings = [];
                foreach ($requests as $key => $request) {
                    $possibleStrings[] = $request.' extends FormRequest';
                }

                return Str::contains($file->file_contents, $possibleStrings);
            })
            ->all();

        $file_paths = [];

        foreach ($files as $key => $file) {
            $file_paths[] = $file->file_path_from_base;
        }

        return count($file_paths) ? $file_paths : false;
    }

    /**
     * Model CrudRequest absolute path.
     *
     * @return string
     */
    public function getCrudRequestPathsAttribute()
    {
        if ($this->crud_requests) {
            $requests = (array) $this->crud_requests;

            foreach ($requests as $key => $request) {
                $requests[$key] = base_path($request);
            }

            return count($requests) > 1 ? $requests : (count($requests) ? $requests[0] : false);
        }
    }

    /**
     * Model CrudRequest absolute path.
     *
     * @return string
     */
    public function getCrudRequestFilesAttribute()
    {
        $path = $this->crud_request_paths;

        if ($path == '') {
            return '';
        }

        if (is_string($path)) {
            return new CustomFile($path);
        }

        if (is_array($path)) {
            $files = [];

            foreach ($path as $key => $item) {
                $files[] = new CustomFile($item);
            }

            return $files;
        }

        return '';
    }

    /**
     * Find the Model migration.
     *
     * @return bool
     */
    public function getMigrationAttribute()
    {
        $table = $this->getTableAttribute();
        $migrations = [];

        $files = CustomFile::allFrom(config('backpack.devtools.paths.migrations'))
            ->filter(function ($file) {
                return $file->isMigration();
            })
            ->filter(function ($file) use ($table) {
                return Str::contains($file->file_contents, [
                    'Schema::create(\''.$table,
                    'Schema::create("'.$table,
                ]);
            })
            ->all();

        $file_paths = [];

        foreach ($files as $key => $file) {
            $file_paths[] = $file->file_path_from_base;
        }

        return count($file_paths) > 1 ? $file_paths : (count($file_paths) ? $file_paths[0] : false);
    }

    /**
     * Model migration absolute path.
     *
     * @return string
     */
    public function getMigrationPathAttribute()
    {
        if ($this->migration) {
            return base_path($this->migration);
        }
    }

    /**
     * Find the Model Factory.
     *
     * @return bool
     */
    public function getFactoryAttribute()
    {
        $classWithNamespace = $this->class_path;
        $className = $this->file_name;

        $paths = config('backpack.devtools.paths.factories');

        $file = CustomFile::allFrom($paths)
            ->filter(function ($file) {
                return $file->isClass() && $file->isFactory();
            })
            ->filter(function ($file) use ($classWithNamespace, $className) {
                $content = Str::of($file->file_contents);

                return $content->contains($classWithNamespace) && $content->match("/model = ['\"]?.*$className(\:\:class)?['\"]?/");
            })
            ->first();

        return $file ?? false;
    }

    /**
     * Model Factory absolute path.
     *
     * @return string
     */
    public function getFactoryPathAttribute()
    {
        if ($this->hasFactory) {
            return $this->factory->file_path_absolute;
        }
    }

    /**
     * Find the Model Seeder.
     *
     * @return bool
     */
    public function getSeederAttribute()
    {
        $classWithNamespace = $this->class_path;
        $className = $this->file_name;

        $paths = config('backpack.devtools.paths.seeders');

        $file = CustomFile::allFrom($paths)
            ->filter(function ($file) {
                return $file->isClass() && $file->isSeeder();
            })
            ->filter(function ($file) use ($classWithNamespace, $className) {
                $content = Str::of($file->file_contents);

                return $content->contains($classWithNamespace) && $content->contains("$className::factory()");
            })
            ->first();

        return $file ?? false;
    }

    /**
     * Model Seeder absolute path.
     *
     * @return string
     */
    public function getSeederPathAttribute()
    {
        if ($this->hasSeeder) {
            return $this->seeder->file_path_absolute;
        }
    }

    /**
     * Get the name of that model's DB table.
     *
     * @return string the string specified as table name inside the model file
     */
    public function getTableAttribute()
    {
        return $this->instance->getTable();
    }

    /**
     * Check if the database table exists in the DBMS.
     *
     * @return bool
     */
    public function getTableExistsAttribute()
    {
        return $this->tableExists();
    }

    /**
     * Get a model instance.
     */
    public function getInstanceAttribute()
    {
        return app($this->class_path);
    }

    /**
     * Get the clear name of the model (no prefix, no extension, nothing).
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->file_name;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | CHECKS
    |--------------------------------------------------------------------------
    */

    /**
     * Checks if the file is inside the app directory.
     *
     * @return string
     */
    public function isInsideAppDirectory()
    {
        return Str::startsWith($this->file_path_from_base, 'app');
    }
}
