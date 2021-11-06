<?php

namespace Backpack\DevTools\Http\Controllers\Operations;

use Alert;
use Illuminate\Support\Facades\Route;

trait DeleteMigrationOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupDeleteMigrationRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/delete-migration', [
            'as'        => $routeName.'.deleteMigration',
            'uses'      => $controller.'@deleteMigration',
            'operation' => 'deleteMigration',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupDeleteMigrationDefaults()
    {
        $this->crud->allowAccess('deleteMigration');

        $this->crud->operation('deleteMigration', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButton('line', 'deleteMigration', 'view', 'backpack.devtools::buttons.migration_delete', 'end');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function deleteMigration($id)
    {
        $this->crud->hasAccessOrFail('deleteMigration');

        $id = $this->crud->getCurrentEntryId() ?? $id;
        $entry = $this->crud->getEntry($id);
        $output = $entry->deleteFile();

        if ($output === true) {
            Alert::success('The migration has been deleted.')->flash();
        } else {
            Alert::warning('Oops - the migration might not have been deleted.')->flash();
        }

        return redirect()->back();
    }
}
