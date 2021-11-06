<?php

namespace Backpack\DevTools\Http\Controllers;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\DevTools\GeneratorInterface;
use Backpack\DevTools\Http\Requests\ModelRequest;
use Backpack\DevTools\Models\Model;
use Config;
use Widget;

/**
 * Class ModelCrudController.
 *
 * @property \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ModelCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\DevTools\Http\Controllers\Operations\BuildCrudOperation;
    use \Backpack\DevTools\Http\Controllers\Operations\AddCrudTraitToModel;
    use \Backpack\DevTools\Http\Controllers\Operations\RelatedFilesOperation;
    use \Backpack\DevTools\Http\Controllers\Operations\StrippedShowOperation;
    use \Backpack\DevTools\Http\Controllers\Operations\SeedModelOperation;

    public function setup()
    {
        CRUD::setModel(\Backpack\DevTools\Models\Model::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/devtools/model');
        CRUD::setEntityNameStrings('model', 'models');

        Config::set('backpack.base.breadcrumbs', false);

        Widget::add()
            ->to('before_breadcrumbs')
            ->type('view')
            ->view('backpack.devtools::widgets.menu');

        CRUD::operation(['list', 'show'], function () {
            $this->crud->removeButton('build_crud');
            $this->crud->addButton('line', 'model_generate', 'view', 'backpack.devtools::buttons.model_generate', 'end');

            // $this->crud->addButton('top', 'generate_all', 'view', 'backpack.devtools::buttons.generate_all', 'end');
        });
    }

    protected function setupListOperation()
    {
        // refresh the Sushi list, just in case some models were deleted
        Model::clearBootedModels();

        // Columns
        CRUD::column('class_path')
            ->label('Model')
            ->type('view')
            ->view('backpack.devtools::columns.file-link')
            ->filePath('file_path')
            ->searchLogic(function ($query, $column, $searchTerm) {
                $query->where('file_path', 'LIKE', "%$searchTerm%");
            });

        CRUD::column('has_crud_trait')
            ->type('check')
            ->label('Uses CrudTrait')
            ->wrapper([
                'class' => (function ($crud, $column, $entry, $related_key) {
                    return 'text-'.($entry->hasCrudTrait ? 'success' : 'danger');
                }),
            ]);

        CRUD::column('crud_controller')
            ->type('view')
            ->view('backpack.devtools::columns.file-check')
            ->label('Has Controller')
            ->href('crud_controller')
            ->file(function ($entry) {
                return $entry->crudcontroller;
            });

        CRUD::column('has_factory')
            ->type('view')
            ->view('backpack.devtools::columns.file-check')
            ->label('Has Factory')
            ->href('factory')
            ->file(function ($entry) {
                return $entry->factory;
            });

        CRUD::column('has_seeder')
            ->type('view')
            ->view('backpack.devtools::columns.file-check')
            ->label('Has Seeder')
            ->href('seeder')
            ->file(function ($entry) {
                return $entry->seeder;
            });

        CRUD::removeButton('show');

        // order by creation date by default
        CRUD::orderBy('file_created_at', 'desc');

        // TODO:
        // CRUD::column('requests');
        // CRUD::column('migrations');
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

        CRUD::column('crud_controller_path')
            ->label('Crud Controller')
            ->type('view')
            ->view('backpack.devtools::columns.file-link');

        CRUD::column('factory_path')
            ->label('Factory')
            ->type('view')
            ->view('backpack.devtools::columns.file-link');

        CRUD::column('seeder_path')
            ->label('Seeder')
            ->type('view')
            ->view('backpack.devtools::columns.file-link');

        CRUD::column('table')
            ->type('view')
            ->view('backpack.devtools::columns.code-inline');

        CRUD::removeButton('details');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ModelRequest::class);
        CRUD::setCreateContentClass('col-md-12');

        // model name
        CRUD::field('name')
            ->type('text')
            ->label('Name')
            ->prefix(app()->getNamespace().'Models\\')
            ->suffix('.php')
            ->attributes([
                'placeholder' => 'Article',
            ])
            ->hint('Use the entity name in singular and StudlyCase.');

        // form builder
        CRUD::field('form_builder')
            ->label('Migration Schema')
            ->type('livewire_component')
            ->view_namespace('backpack.devtools::fields')
            ->component('migration-schema')
            ->parameters([
                'operation' => CRUD::getCurrentOperation(),
            ]);

        CRUD::field('relationships')
            ->type('livewire_component')
            ->view_namespace('backpack.devtools::fields')
            ->component('relationship-schema')
            ->wrapper([
                'class' => 'form-group col-sm-11 ml-4 pl-4 mb-0 pb-3 border-left',
            ]);

        // separator
        CRUD::field('actions_header')
            ->type('custom_html')
            ->value('<hr /><label>Afterwards:</label>');

        // checkbox to also run migration
        CRUD::field('run_migration')
            ->type('checkbox')
            ->label('Run migration')
            ->default(true);

        // checkbox to also generate Factory
        CRUD::field('generate_factory')
            ->type('checkbox')
            ->label('Generate Factory');

        // checkbox to also generate Seeder
        CRUD::field('generate_seeder')
            ->type('checkbox')
            ->label('Generate Seeder');

        // checkbox to also build the CRUD from that model
        CRUD::field('build_crud')
            ->type('checkbox')
            ->label('Generate CRUD')
            ->default(true);

        CRUD::removeSaveAction('save_and_preview');
        CRUD::addSaveAction([
            'name' => 'save_and_preview',
            'redirect' => function ($crud, $request, $itemId = null) {
                $itemId = $itemId ?: $request->input('id');
                $redirectUrl = $crud->route.'/'.$itemId.'/related-files';
                if ($request->has('locale')) {
                    $redirectUrl .= '?locale='.$request->input('locale');
                }

                return $redirectUrl;
            }, // what's the redirect URL, where the user will be taken after saving?

            // OPTIONAL:
            'button_text' => 'Save and preview', // override text appearing on the button
            'visible' => function ($crud) {
                return true;
            }, // customize when this save action is visible for the current operation
            // 'referrer_url' => function ($crud, $request, $itemId) {
            //     return $crud->route;
            // }, // override http_referrer_url
            'order' => 1, // change the order save actions are in
        ]);

        // CRUD::setSaveAction('save_and_see_files');
    }

    /**
     * Instead of running the default Create operation store method.
     * Run this custom method that uses Blueprint to generate the migration and model.
     *
     * @param GeneratorInterface $name
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
