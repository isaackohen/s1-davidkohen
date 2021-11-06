<?php

namespace Backpack\DevTools\Http\Controllers\Operations;

use Backpack\DevTools\Models\Model;
use Illuminate\Support\Facades\Route;
use Str;

trait SeedModelOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupSeedModelRoutes($segment, $routeName, $controller)
    {
        Route::post($segment.'/{id}/seed-model', [
            'as' => $routeName.'.seedModel',
            'uses' => $controller.'@seedModel',
            'operation' => 'seedModel',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupSeedModelDefaults()
    {
        $this->crud->allowAccess('seedModel');

        $this->crud->operation('seedModel', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButton('line', 'seed_model', 'view', 'backpack.devtools::buttons.seed_model', 'end');
        });
    }

    /**
     * Show the form for creating inserting a new row.
     *
     * @return Response
     */
    public function seedModel($id)
    {
        // Access
        $this->crud->hasAccessOrFail('seedModel');

        // Get Model
        $model = Model::find($id);

        // Options
        $CREATE = 1;
        $TRUNCATE = 2;

        $count = request()->input('count');
        $option = request()->input('option');

        // Truncate table
        if ($option & $TRUNCATE) {
            $model->instance->truncate();
            $messages[] = 'Table was truncated.';
        }

        // Create dummy entries
        if ($option & $CREATE) {
            try {
                $model->instance->factory()->count($count)->create();
            } catch (\Throwable $e) {
                return response()->json([
                    'title' => 'Seeder failed',
                    'message' => $e->getMessage(),
                ], 500);
            } catch (\Exception $e) {
                $file = collect($e->getTrace())
                    ->first(function ($value) {
                        return ! Str::of($value['file'] ?? '')->startsWith(base_path().'\vendor');
                    });

                $file = Str::of($file['file'])->after(base_path().'\\').':'.$file['line'];

                return response()->json([
                    'title' => 'Seeder failed',
                    'message' => $e->getMessage().'<br />On '.$file,
                ], 500);
            }
            $messages[] = trans_choice('{1} :count dummy entry has been created.|[2,*] :count dummy entries have been created.', $count, ['count' => $count]);
        }

        return response()->json([
            'title' => 'Success',
            'message' => implode(' ', $messages),
        ]);
    }
}
