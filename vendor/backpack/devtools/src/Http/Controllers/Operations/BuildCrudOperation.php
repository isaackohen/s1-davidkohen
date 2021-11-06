<?php

namespace Backpack\DevTools\Http\Controllers\Operations;

use Alert;
use Artisan;
use Backpack\DevTools\GeneratorInterface;
use Backpack\DevTools\Generators\AlertConstructor;
use Backpack\DevTools\Models\Model;
use Backpack\Generators\Console\Commands\BuildBackpackCommand;
use Backpack\Generators\Console\Commands\CrudBackpackCommand;
use File;
use Illuminate\Support\Facades\Route;
use Redirect;
use Str;

trait BuildCrudOperation
{
    use AlertConstructor;

    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  prefix of the route name
     * @param string $controller name of the current CrudController
     */
    protected function setupBuildCrudRoutes($segment, $routeName, $controller)
    {
        // entry operations
        Route::get($segment.'/{id}/build-crud', [
            'as' => $routeName.'.buildCrud',
            'uses' => $controller.'@buildCrud',
            'operation' => 'buildCrud',
        ]);

        Route::get($segment.'/{id}/build-factory', [
            'as' => $routeName.'.buildFactory',
            'uses' => $controller.'@buildFactory',
            'operation' => 'buildFactory',
        ]);

        Route::get($segment.'/{id}/build-seeder', [
            'as' => $routeName.'.buildSeeder',
            'uses' => $controller.'@buildSeeder',
            'operation' => 'buildSeeder',
        ]);

        Route::get($segment.'/{id}/build-all', [
            'as' => $routeName.'.buildAll',
            'uses' => $controller.'@buildAll',
            'operation' => 'buildCrud',
        ]);

        // All entries operations
        Route::get($segment.'/build-all-cruds', [
            'as' => $routeName.'.buildAllCruds',
            'uses' => $controller.'@buildAllCruds',
            'operation' => 'buildCrud',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupBuildCrudDefaults()
    {
        $this->crud->allowAccess('buildCrud');

        $this->crud->operation('buildCrud', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButton('line', 'build_crud', 'view', 'backpack.devtools::buttons.build_crud', 'end');
            $this->crud->addButton('top', 'build_all_cruds', 'view', 'backpack.devtools::buttons.build_all_cruds', 'end');
        });
    }

    /**
     * Build the CRUD Controller.
     *
     * @param int $id
     *
     * @return Response
     */
    public function buildCrud($id)
    {
        $this->crud->hasAccessOrFail('buildCrud');
        $entry = $this->crud->getCurrentEntry();

        if (! $entry->tableExists()) {
            Alert::error('<strong>Database table doesn\'t exist.</strong><br>Please run the migration first.')->flash();

            return Redirect::back();
        }

        $name = $entry->name;
        $namespace = $entry->class_namespace;

        // call the artisan method that creates a CRUD for this model
        Artisan::call(CrudBackpackCommand::class, ['name' => $name]);

        // Replace namespace on controller
        $controller = app_path('Http/Controllers/Admin/'.Str::studly($name).'CrudController.php');
        if (File::exists($controller)) {
            $file = Str::of(File::get($controller))->replace('CRUD::setModel(\\App\\Models\\', "CRUD::setModel(\\$namespace\\");
            File::put($controller, $file);
        }

        // Alert
        $alert = $this->alertTitle('CRUD');
        $alert .= Str::of(Artisan::output())
            ->trim()
            ->explode(PHP_EOL)
            ->map(function ($entry) {
                return $this->alertLine($entry);
            })
            ->join('');

        Alert::success($alert)->flash();

        return Redirect::back();
    }

    /**
     * Build Seeder.
     *
     * @param int $id
     *
     * @return Response
     */
    public function buildSeeder($id, GeneratorInterface $generator)
    {
        $this->crud->hasAccessOrFail('buildCrud');
        $blueprint = base_path('.blueprint');
        $entry = $this->crud->getCurrentEntry();

        $result = $generator->buildSeeder($entry);

        Alert::success($result)->flash();

        return Redirect::back();
    }

    /**
     * Build Factory.
     *
     * @param int $id
     *
     * @return Response
     */
    public function buildFactory($id, GeneratorInterface $generator)
    {
        $this->crud->hasAccessOrFail('buildCrud');
        $blueprint = base_path('.blueprint');
        $entry = $this->crud->getCurrentEntry();

        $result = $generator->buildFactory($entry);

        Alert::success($result)->flash();

        return Redirect::back();
    }

    /**
     * Build All - CRUD, Seeder and Factory.
     *
     * @param int $id
     *
     * @return Response
     */
    public function buildAll($id, GeneratorInterface $generator)
    {
        $entry = Model::find($id);

        if ($entry->can_generate_crud) {
            $this->buildCrud($id, $generator);
        }

        if ($entry->can_generate_factory) {
            $this->buildFactory($id, $generator);
        }

        if ($entry->can_generate_seeder) {
            $this->buildSeeder($id, $generator);
        }

        return Redirect::back();
    }

    /**
     * Build CRUDs for all models.
     *
     * @return Response
     */
    public function buildAllCruds()
    {
        $this->crud->hasAccessOrFail('buildCrud');

        // call the artisan method that creates a CRUD for this model
        Artisan::call(BuildBackpackCommand::class);

        $alert = $this->alertTitle('CRUD generated for:');
        $alert .= Str::of(Artisan::output())
            ->trim()
            ->explode(PHP_EOL)
            ->filter(function ($entry) {
                return str_contains($entry, '---');
            })
            ->map(function ($entry) {
                return $this->alertLine(str_replace('---', '', $entry), 'model.');
            })
            ->join('');

        Alert::success($alert)->flash();

        return Redirect::back();
    }
}
