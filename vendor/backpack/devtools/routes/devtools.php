<?php

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin').'/devtools',
    'middleware' => ['web', config('backpack.base.middleware_key', 'admin')],
    'namespace' => 'Backpack\DevTools\Http\Controllers',
    'name' => 'devtools.',
], function () {
    Route::get('/', function () {
        return redirect(config('backpack.base.route_prefix', 'admin').'/devtools/model');
    });

    Route::crud('model', 'ModelCrudController');
    Route::crud('migration', 'MigrationCrudController');

    Route::get('dump-autoload', 'DumpAutoload');
});
