<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PromocodeRequest;
use App\Providers;
use App\Utils\APIResponse;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class PromocodeCrudController
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PromocodeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Promocode::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/promocode');
        CRUD::setEntityNameStrings('promocode', 'promocodes');
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
          'name' => 'code', // The db column name
          'label' => 'Promocode', // Table column heading
          'type' => 'Text',
        ]);

        $this->crud->addColumn([
          'name' => 'sum', // The db column name
          'label' => 'Sum ($)', // Table column heading
          'type' => 'Text',
        ]);

        $this->crud->addColumn([
          'name' => 'currency', // The db column name
          'label' => 'Currency', // Table column heading
          'type' => 'Text',
        ]);

        $this->crud->addColumn([
          'name' => 'usages', // The db column name
          'label' => 'Use Limit', // Table column heading
          'type' => 'Text',
        ]);

        $this->crud->addColumn([
          'name' => 'times_used', // The db column name
          'label' => 'Used', // Table column heading
          'type' => 'Text',
        ]);

        $this->crud->addColumn([
          'name' => 'expires', // The db column name
          'label' => 'Expires', // Table column heading
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
        CRUD::setValidation(PromocodeRequest::class);

        $this->crud->addField([
          'name' => 'code', // The db column name
          'label' => 'Promocode', // Table column heading
          'default' => uniqid(),
          'type' => 'text',
        ]);

        $this->crud->addField([
          'name' => 'sum', // The db column name
          'label' => 'Sum', // Table column heading
          'type' => 'text',
        ]);

        $this->crud->addField([
            'name'        => 'currency', // the name of the db column
            'label'       => 'Currency', // the input label
            'model'     => \App\Currency::class, // foreign key model
            'type'        => 'select2',

      'options'   => (function ($query) {
          return $query->orderBy('currency', 'ASC')->get();
      }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select

            // optional
            //'inline'      => false, // show the radios all on the same line?
        ]);

        $this->crud->addField([
          'name' => 'currency', // The db column name
          'label' => 'Currency', // Table column heading
          'type' => 'text',
        ]);

        $this->crud->addField([
          'name' => 'usages', // The db column name
          'label' => 'Use Limit', // Table column heading
          'type' => 'text',
        ]);

        $this->crud->addField([
          'name' => 'times_used', // The db column name
          'label' => 'Used', // Table column heading
          'type' => 'text',
        ]);

        $this->crud->addField([
          'name' => 'expires', // The db column name
          'label' => 'Expires', // Table column heading
          'type' => 'date',
        ]);

        $this->crud->addField([
            'name'  => 'used',
            'type'  => 'json',
            'view_namespace' => 'json-field-for-backpack::fields',

            // OPTIONAL

            // Which modes should the JsonEditor JS plugin allow?
            // Please note that the first mode in the array will be used as the default mode.
            'modes' => ['tree'],

            // Default value, if needed. If there is an actual value in the json column,
            // it will do an array_merge_recursive(), with the json column values
            // replacing the ones with the same keys.
            'default' => [],
        ]);
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
