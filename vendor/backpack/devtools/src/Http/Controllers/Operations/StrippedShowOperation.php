<?php

namespace Backpack\DevTools\Http\Controllers\Operations;

use Alert;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait StrippedShowOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupStrippedShowOperationRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/{id}/stripped-show', [
            'as'        => $routeName.'.strippedShow',
            'uses'      => $controller.'@strippedShow',
            'operation' => 'strippedShow',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupStrippedShowOperationDefaults()
    {
        $this->crud->allowAccess('strippedShow');

        $this->crud->operation('strippedShowOperation', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });
    }

    /**
     * Return the HTML of the Preview (aka Show) page without the sidebar,
     * topbar, headings, etc. Just the content itself.
     *
     * @return Response
     */
    public function strippedShow($id)
    {
        $this->crud->hasAccessOrFail('show');
        $this->crud->setOperation('show');
        $this->crud->setShowView('backpack.devtools::operations.show-stripped');

        $this->setupConfigurationForCurrentOperation();
        $this->setupShowDefaults();
        $this->setupShowOperation();

        return $this->show($id);
    }
}
