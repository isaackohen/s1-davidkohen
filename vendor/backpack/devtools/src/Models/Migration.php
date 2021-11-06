<?php

namespace Backpack\DevTools\Models;

use Artisan;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Backpack\DevTools\CustomFile;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Sushi\Sushi;

class Migration extends EloquentModel
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
        // 'table',
        // 'executed',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public $sushiInsertChunkSize = 40;

    /*
    |--------------------------------------------------------------------------
    | SUSHI-SPECIFIC METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Instead of looking inside the database for results, this method is called
     * by Sushi to provide all rows for this Eloquent model (the Model model).
     *
     * @return array
     */
    public function getRows()
    {
        $paths = config('backpack.devtools.paths.migrations');

        return CustomFile::allFrom($paths)
            ->filter(function ($file, $key) {
                return $file->isPhp() && $file->isMigration();
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
     * Fake the existance of the db table, since this is a Sushi Model.
     *
     * @return bool
     */
    public function tableExists()
    {
        return true;
    }

    /**
     * Run that specific migration file (and only that one).
     *
     * @return bool|string returns TRUE in case of success, error message in case of failure
     */
    public function run()
    {
        Artisan::call('migrate', ['--path' => $this->file_path_from_base, '--force' => true]);

        $output_string = Artisan::output();
        $output_array = explode(PHP_EOL, $output_string);

        foreach ($output_array as $key => $line) {
            if (Str::of($line)->startsWith('Migrated')) {
                return true;
            }
        }

        return $output_string;
    }

    /**
     * Rollback that specific migration file (and only that one).
     *
     * @return bool|string returns TRUE in case of success, error message in case of failure
     */
    public function rollback()
    {
        Artisan::call('migrate:rollback', ['--path' => $this->file_path_from_base, '--force' => true]);

        $output_string = Artisan::output();
        $output_array = explode(PHP_EOL, $output_string);

        foreach ($output_array as $key => $line) {
            if (Str::of($line)->startsWith('Rolled back')) {
                return true;
            }
        }

        return $output_string;
    }

    /**
     * Delete this migration file from its directory.
     *
     * @return bool
     */
    public function deleteFile()
    {
        // delete the file from the directory
        $file_delete_status = unlink($this->file);

        // delete the entry from Sushi
        $entry_delete_status = $this->delete();

        // refresh the Sushi entries
        static::clearBootedModels();

        return $file_delete_status && $entry_delete_status;
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
     * Check if the migration has already been run.
     *
     * @return bool
     */
    public function getExecutedAttribute()
    {
        // TODO: check if the migrations table is even there

        return (bool) DB::table('migrations')->where('migration', $this->file_name)->count();
    }

    /**
     * Check if the migration has already been run.
     *
     * @return bool
     */
    public function getDateAttribute()
    {
        $date = Str::of($this->file_name)->substr(0, 17);

        return Carbon::createFromFormat('Y_m_d_His', $date);
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

    /**
     * Get the title of the model.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        return Str::of($this->file_name)->substr(18)->replace('_', ' ')->ucfirst();
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
