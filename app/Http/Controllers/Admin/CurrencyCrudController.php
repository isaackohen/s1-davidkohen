<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CurrencyRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CurrencyCrudController
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CurrencyCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Currency::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/currency');
        CRUD::setEntityNameStrings('currency', 'currencies');
        $this->crud->enableDetailsRow();
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
          'name' => 'currency', // The db column name
          'label' => 'Currency ID', // Table column heading
          'type' => 'Text',
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CurrencyRequest::class);

        $this->crud->addField([
        'name' => 'currency',
        'type' => 'text',
        'label' => 'Currency ID',

      ]);

        $this->crud->addField([
    'name'  => 'data',
    'type'  => 'json',
    'view_namespace' => 'json-field-for-backpack::fields',

    // OPTIONAL

    // Which modes should the JsonEditor JS plugin allow?
    // Please note that the first mode in the array will be used as the default mode.
    'modes' => ['form', 'tree', 'code'],

    // Default value, if needed. If there is an actual value in the json column,
    // it will do an array_merge_recursive(), with the json column values
    // replacing the ones with the same keys.
    'default' => [],
]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
