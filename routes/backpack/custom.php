<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::prefix(config('backpack.base.route_prefix', 'admin'))->middleware(array_merge((array) config('backpack.base.web_middleware', 'web'), (array) config('backpack.base.middleware_key', 'admin')))->namespace('App\Http\Controllers\Admin')->group([
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),], function () { // custom admin routes
    Route::crud('statistics', 'StatisticsCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('admin-activity', 'AdminActivityCrudController');
    Route::crud('providers', 'ProvidersCrudController');
    Route::crud('promocode', 'PromocodeCrudController');
    Route::crud('currency', 'CurrencyCrudController');
    Route::crud('gameslist', 'GameslistCrudController');
    Route::get('providers/{id}/disableProvider', 'ProvidersCrudController@disableGames');
    Route::get('providers/{id}/enableProvider', 'ProvidersCrudController@enableProvider');
    Route::get('providers/updateProviders', 'ProvidersCrudController@updateProviderList');

    Route::crud('settings', 'SettingsCrudController');
}); // this should be the absolute last line of this file
