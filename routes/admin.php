<?php

use App\Http\Controllers\Admin\CurrenciesController;
use App\Http\Controllers\Admin\GamesController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletController;

Route::get('/{vue_capture?}', 'MainController@main')->where('vue_capture', '[\\/\\w\\:.-]*');

Route::prefix('stats')->group(function () {
    Route::post('games', 'MainController@games');
    Route::post('analytics', 'MainController@analytics');
    Route::post('deposits', 'MainController@deposits');
});

Route::post('/info', 'MainController@info');
Route::post('user', 'UserController@user');
Route::post('users', 'UserController@users');
Route::post('checkDuplicates', 'UserController@checkDuplicates');
Route::post('/ban', 'UserController@ban');
Route::post('/role', 'UserController@role');
Route::post('/balance', 'UserController@balance');
Route::post('ethereumNativeSendDeposits', 'WalletController@ethereumNativeSendDeposits');

Route::prefix('wallet')->group(function () {
    Route::post('info', 'WalletController@info');
    Route::post('infoIgnored', 'WalletController@infoIgnored');
    Route::post('accept', 'WalletController@accept');
    Route::post('decline', 'WalletController@decline');
    Route::post('ignore', 'WalletController@ignore');
    Route::post('unignore', 'WalletController@unignore');
    Route::get('autoSetup', 'WalletController@autoSetup');
    Route::post('/transfer', 'WalletController@transfer');
});

Route::prefix('notifications')->group(function () {
    Route::post('/browser', 'NotificationsController@browser');
    Route::post('/standalone', 'NotificationsController@standalone');
    Route::post('/global', 'NotificationsController@global');
    Route::post('/global_remove', 'NotificationsController@globalRemove');
});

Route::post('/notifications/data', 'NotificationsController@notificationsData');
Route::post('/toggle_module', 'ModuleController@toggle');
Route::post('/option_value', 'ModuleController@setValue');
Route::post('/option_value', 'ModuleController@setValue');
Route::post('modules', 'ModuleController@setData');
Route::post('/toggle', 'GamesController@toggle');
Route::post('/extToggle', 'GamesController@extToggle');

Route::prefix('extgames')->group(function () {
    Route::post('settings', 'GamesController@settings');
    Route::post('games', 'GamesController@games');
});

Route::post('/currencyOption', 'CurrenciesController@currencyOption');
Route::post('/currencySettings', 'CurrenciesController@currencySettings');
Route::post('/toggleCurrency', 'CurrenciesController@toggleCurrency');
Route::post('/currencyBalance', 'CurrenciesController@currencyBalance');
Route::post('/activity', 'MainController@activity');

Route::prefix('settings')->group(function () {
    Route::post('get', 'SettingsController@get');
    Route::post('create', 'SettingsController@create');
    Route::post('edit', 'SettingsController@edit');
    Route::post('remove', 'SettingsController@remove');
});

Route::prefix('bot')->group(function () {
    Route::post('settings', 'SettingsController@botSettings');
    Route::post('start', 'SettingsController@startBot');
});

Route::prefix('promocode')->group(function () {
    Route::post('get', 'PromocodeController@get');
    Route::post('remove', 'PromocodeController@remove');
    Route::post('remove_inactive', 'PromocodeController@removeInactive');
    Route::post('create', 'PromocodeController@create');
});
