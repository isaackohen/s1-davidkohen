<?php

namespace Backpack\DevTools\Http\Controllers\Operations;

use Alert;
use Illuminate\Support\Facades\Route;

trait RunMigrationOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRunMigrationRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/run-migration', [
            'as' => $routeName.'.runMigration',
            'uses' => $controller.'@runMigration',
            'operation' => 'runMigration',
        ]);

        Route::get($segment.'/{id}/rollback-migration', [
            'as' => $routeName.'.rollbackMigration',
            'uses' => $controller.'@rollbackMigration',
            'operation' => 'rollbackMigration',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRunMigrationDefaults()
    {
        $this->crud->allowAccess('runMigration');

        $this->crud->operation('runMigration', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButton('line', 'runMigration', 'view', 'backpack.devtools::buttons.migration_run', 'end');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function runMigration($id)
    {
        $this->crud->hasAccessOrFail('runMigration');

        $id = $this->crud->getCurrentEntryId() ?? $id;
        $entry = $this->crud->getEntry($id);
        $output = $entry->run();

        if ($output === true) {
            Alert::success('The migration has been run successfully.')->flash();
        } else {
            Alert::warning(nl2br($output))->flash();
        }

        return redirect()->back();
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function rollbackMigration($id)
    {
        $this->crud->hasAccessOrFail('runMigration');

        $id = $this->crud->getCurrentEntryId() ?? $id;
        $entry = $this->crud->getEntry($id);
        $output = $entry->rollback();

        if ($output === true) {
            Alert::success('The migration has been rolled back successfully.')->flash();
        } else {
            Alert::warning(nl2br($output))->flash();
        }

        return redirect()->back();
    }
}
