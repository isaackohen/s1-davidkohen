<?php

namespace Backpack\DevTools\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\DevTools\GeneratorInterface;
use Backpack\DevTools\Http\Requests\MigrationRequest;
use Backpack\DevTools\Models\Migration;
use Config;
use Widget;

/**
 * Class MigrationCrudController.
 *
 * @property \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MigrationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\DevTools\Http\Controllers\Operations\RunMigrationOperation;
    use \Backpack\DevTools\Http\Controllers\Operations\DeleteMigrationOperation;
    use \Backpack\DevTools\Http\Controllers\Operations\StrippedShowOperation;
    // use \Backpack\DevTools\Http\Controllers\Operations\BuildCrudOperation;

    public function setup()
    {
        CRUD::setModel(Migration::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/devtools/migration');
        CRUD::setEntityNameStrings('migration', 'migrations');

        Config::set('backpack.base.breadcrumbs', false);

        Widget::add()
            ->to('before_breadcrumbs')
            ->type('view')
            ->view('backpack.devtools::widgets.menu');
    }

    protected function setupListOperation()
    {
        // refresh the Sushi list, just in case some migrations were deleted
        Migration::clearBootedModels();

        // Columns
        CRUD::column('name')
            ->label('File name')
            ->type('view')
            ->view('backpack.devtools::columns.file-link')
            ->filePath('file_path')
            ->searchLogic(function ($query, $column, $searchTerm) {
                $query->where('file_path', 'LIKE', '%'.$searchTerm.'%');
            })
            ->limit(100);

        CRUD::column('file_created_at')
            ->type('datetime')
            ->label('Date');

        CRUD::column('executed')
            ->type('check')
            ->wrapper([
                'title' => 'Shows if the migration has been run.',
                'class' => (function ($crud, $column, $entry, $related_key) {
                    return 'text-'.($entry->executed ? 'success' : 'danger');
                }),
            ]);

        CRUD::removeButton('show');

        CRUD::addButton('line', 'details', 'view', 'backpack.devtools::buttons.details', 'beginning');

        // TODO:
        // - "Run" button and operation, to run one specific migration
        // - "Rollback" button to rollback that migration
        // - "Delete" button to delete that migration file and rolls back the migration

        // - "Run all" button and operation, to basically run `php artisan migrate`
        // - "Rollback" button
        // - "Generate" drodown or modal to pick one/more things to generate:
        //      - Model
        //      - Factory
        //      - Seeder
        //      - CRUD
        // - "Add multiple" button (add multiple tables in one screen)

        // order by filename by default
        CRUD::orderBy('file_name', 'desc');
    }

    protected function setupShowOperation()
    {
        CRUD::setShowContentClass('col-md-12');

        CRUD::set('show.setFromDb', false);

        CRUD::column('file_path_from_base')
            ->type('view')
            ->view('backpack.devtools::columns.file-link')
            ->filePath('file_path');

        CRUD::column('file_contents')
            ->type('view')
            ->view('backpack.devtools::columns.code');

        CRUD::column('file_created_at')
            ->type('datetime')
            ->label('Date');

        CRUD::column('executed')
            ->type('check')
            ->wrapper([
                'title' => 'Shows if the migration has been run.',
                'class' => (function ($crud, $column, $entry, $related_key) {
                    return 'text-'.($entry->executed ? 'success' : 'danger');
                }),
            ]);

        CRUD::removeButton('details');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(MigrationRequest::class);
        CRUD::setCreateContentClass('col-md-12');

        CRUD::field('table')
            ->type('text')
            ->attributes([
                'placeholder' => 'articles',
            ])
            ->label('Table name')
            ->hint('Use the entity name in plural and snake_case.');

        CRUD::field('migration_schema')
            ->label('Migration Schema')
            ->type('livewire_component')
            ->view_namespace('backpack.devtools::fields')
            ->component('migration-schema')
            ->parameters([
                'operation' => CRUD::getCurrentOperation(),
            ]);

        CRUD::field('actions_header')
            ->type('custom_html')
            ->value('<hr /><label>Afterwards:</label>');

        CRUD::field('run_migration')
            ->type('checkbox')
            ->label('Run migration')
            ->default(true);

        CRUD::field('generate_model')
            ->type('view')
            ->view('backpack.devtools::fields.checkbox_toggler')
            ->label('Generate an Eloquent Model')
            ->wrapper([
                'toggles' => 'generate_model',
            ])
            ->default(true);

        // TODO: make this define relationships, not db columns
        CRUD::field('relationships')
            ->type('livewire_component')
            ->view_namespace('backpack.devtools::fields')
            ->component('relationship-schema')
            ->wrapper([
                'class' => 'form-group col-sm-11 ml-4 pl-4 mb-0 pb-3 border-left',
                'toggler' => 'generate_model',
            ]);

        // checkbox to generate the Factory
        CRUD::field('generate_factory')
            ->type('checkbox')
            ->wrapper([
                'class' => 'form-group col-sm-12 ml-4 pl-4 mb-0 pb-3 border-left',
                'toggler' => 'generate_model',
            ])
            ->label('Generate a Factory');

        // checkbox to generate the Seeder
        CRUD::field('generate_seeder')
            ->type('checkbox')
            ->wrapper([
                'class' => 'form-group col-sm-12 ml-4 pl-4 mb-0 pb-3 border-left',
                'toggler' => 'generate_model',
            ])
            ->label('Generate a Seeder');

        // checkbox to build the CRUD from that model
        CRUD::field('build_crud')
            ->type('checkbox')
            ->wrapper([
                'class' => 'form-group col-sm-12 ml-4 pl-4 mb-0 pb-3 border-left',
                'toggler' => 'generate_model',
            ])
            ->label('Generate a Backpack CRUD for the generated Model')
            ->default(true);
    }

    /**
     * Instead of running the default Create operation store method.
     * Run this custom method that uses Blueprint to generate the migration and model.
     *
     * @param GeneratorInterface $generator
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GeneratorInterface $generator)
    {
        CRUD::hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = CRUD::validateRequest();

        // build migration - create and run blueprint yaml
        $key = $generator->generate($request);

        // save the redirect choice for next time
        CRUD::setSaveAction();

        return CRUD::performSaveAction($key);
    }
}
