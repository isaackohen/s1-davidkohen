<?php

namespace Backpack\DevTools\Http\Controllers\Operations;

use Alert;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait RelatedFilesOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupRelatedFilesOperationRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/related-files/{file?}', [
            'as'        => $routeName.'.relatedFiles',
            'uses'      => $controller.'@relatedFiles',
            'operation' => 'relatedFiles',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupRelatedFilesOperationDefaults()
    {
        $this->crud->allowAccess('relatedFiles');

        $this->crud->operation('relatedFilesOperation', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list'], function () {
            $this->crud->addButton('line', 'relatedFilesButton', 'view', 'backpack.devtools::buttons.related_files');
        });
    }

    /**
     * Show all related files we can think of, each in their own tab.
     *
     * @return Response
     */
    public function relatedFiles($id, $file = 'model')
    {
        $this->crud->hasAccessOrFail('relatedFiles');

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? 'Related files for '.$this->crud->entity_name;
        $this->data['selected'] = $file;

        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view('backpack.devtools::operations.related-files', $this->data);
    }
}
