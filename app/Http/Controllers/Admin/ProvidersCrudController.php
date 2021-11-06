<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProvidersRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\DB;
use App\Gameslist;
use App\Utils\APIResponse;
use \App\Providers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class ProvidersCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProvidersCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    //use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
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
        CRUD::setModel(\App\Providers::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/providers');
        CRUD::setEntityNameStrings('provider', 'providers');

    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addButtonFromView('line', 'test', 'test', 'end');
        $this->crud->addButtonFromView('top', 'updateProvider', 'updateProvider', 'beginning');




        $this->crud->addColumn([
          'name' => 'provider', // The db column name
          'label' => "Provider Name", // Table column heading
          'type' => 'Text',

        ]);

        $this->crud->addColumn([
          'name' => 'img', // The db column name
          'label' => "Image", // Table column heading
          'type' => 'Text'
        ]);

        $this->crud->addColumn([
          'name' => 'ggr', // The db column name
          'label' => "GGR% Cost", // Table column heading
          'type' => 'Text'
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
        CRUD::setValidation(ProvidersRequest::class);

      $this->crud->addField([
        'name' => 'provider',
        'type' => 'text',
        'label' => "Provider",
        'tab' => 'Edit Provider'
      ]);

      $this->crud->addField([
        'name' => 'img',
        'type' => 'text',
        'label' => "Image",
        'tab' => 'Edit Provider'
      ]);
      $this->crud->addField([
        'name' => 'ggr',
        'type' => 'text',
        'label' => "GGR% Cost",
        'tab' => 'Edit Provider'
      ]);

    }


     public function enableProvider($id) {

      $countGames = Gameslist::where('provider', '=', $id)->count();
      $updateGames = Gameslist::where('provider', '=', $id)->update([
        'd' => 0
      ]);
      $updateGames = Providers::where('provider', '=', $id)->update([
        'disabled' => 0
      ]);
            return redirect('/admin/providers');
    }    

     public function disableGames($id) {

      $countGames = Gameslist::where('provider', '=', $id)->count();
      $updateGames = Gameslist::where('provider', '=', $id)->update([
        'd' => 1
      ]);
      $updateGames = Providers::where('provider', '=', $id)->update([
        'disabled' => 1
      ]);
            return redirect('/admin/providers');
    }    



     public function updateProviderList() 
     {
      $apikey = env('API_KEY');
      $response = Http::get('https://api.dk.games/v2/listProviders?apikey='.$apikey);
      $arrayList = array('data' => $response->json()) ;
      Log::info($arrayList);
      foreach($arrayList['data'] as $providing) {
        DB::collection('providers')->where('provider', $providing['provider'])->update(['img' => $providing['img'], 'ggr' => $providing['ggr'], 'games' => $providing['games']], ['upsert' => true]);
        }
        \Alert::add('success', '<strong>Updated Providers from API!</strong><br>Please note: GGR shown here is simply for you to easily see costs of provider.
          <br>Changing GGR on your database here does not actually change anything on API.')->flash();

            return redirect('/admin/providers');
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
