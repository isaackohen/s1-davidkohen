<?php

use App\Http\Controllers\CurrenciesController;
use App\Http\Controllers\GamesController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PromocodeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CurrenciesController;
use App\Http\Controllers\Admin\GamesController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletController;

Route::get('/{vue_capture?}', [MainController::class, 'main'])->where('vue_capture', '[\\/\\w\\:.-]*');

Route::prefix('stats')->group(function () {
    Route::post('games', [MainController::class, 'games']);
    Route::post('analytics', [MainController::class, 'analytics']);
    Route::post('deposits', [MainController::class, 'deposits']);
});

Route::post('/info', [MainController::class, 'info']);
Route::post('user', [UserController::class, 'user']);
Route::post('users', [UserController::class, 'users']);
Route::post('checkDuplicates', [UserController::class, 'checkDuplicates']);
Route::post('/ban', [UserController::class, 'ban']);
Route::post('/role', [UserController::class, 'role']);
Route::post('/balance', [UserController::class, 'balance']);
Route::post('ethereumNativeSendDeposits', [WalletController::class, 'ethereumNativeSendDeposits']);

Route::prefix('wallet')->group(function () {
    Route::post('info', [WalletController::class, 'info']);
    Route::post('infoIgnored', [WalletController::class, 'infoIgnored']);
    Route::post('accept', [WalletController::class, 'accept']);
    Route::post('decline', [WalletController::class, 'decline']);
    Route::post('ignore', [WalletController::class, 'ignore']);
    Route::post('unignore', [WalletController::class, 'unignore']);
    Route::get('autoSetup', [WalletController::class, 'autoSetup']);
    Route::post('/transfer', [WalletController::class, 'transfer']);
});

Route::prefix('notifications')->group(function () {
    Route::post('/browser', [NotificationsController::class, 'browser']);
    Route::post('/standalone', [NotificationsController::class, 'standalone']);
    Route::post('/global', [NotificationsController::class, 'global']);
    Route::post('/global_remove', [NotificationsController::class, 'globalRemove']);
});

Route::post('/notifications/data', [NotificationsController::class, 'notificationsData']);
Route::post('/toggle_module', [ModuleController::class, 'toggle']);
Route::post('/option_value', [ModuleController::class, 'setValue']);
Route::post('/option_value', [ModuleController::class, 'setValue']);
Route::post('modules', [ModuleController::class, 'setData']);
Route::post('/toggle', [GamesController::class, 'toggle']);
Route::post('/extToggle', [GamesController::class, 'extToggle']);

Route::prefix('extgames')->group(function () {
    Route::post('settings', [GamesController::class, 'settings']);
    Route::post('games', [GamesController::class, 'games']);
});

Route::post('/currencyOption', [CurrenciesController::class, 'currencyOption']);
Route::post('/currencySettings', [CurrenciesController::class, 'currencySettings']);
Route::post('/toggleCurrency', [CurrenciesController::class, 'toggleCurrency']);
Route::post('/currencyBalance', [CurrenciesController::class, 'currencyBalance']);
Route::post('/activity', [MainController::class, 'activity']);

Route::prefix('settings')->group(function () {
    Route::post('get', [SettingsController::class, 'get']);
    Route::post('create', [SettingsController::class, 'create']);
    Route::post('edit', [SettingsController::class, 'edit']);
    Route::post('remove', [SettingsController::class, 'remove']);
});

Route::prefix('bot')->group(function () {
    Route::post('settings', [SettingsController::class, 'botSettings']);
    Route::post('start', [SettingsController::class, 'startBot']);
});

Route::prefix('promocode')->group(function () {
    Route::post('get', [PromocodeController::class, 'get']);
    Route::post('remove', [PromocodeController::class, 'remove']);
    Route::post('remove_inactive', [PromocodeController::class, 'removeInactive']);
    Route::post('create', [PromocodeController::class, 'create']);
});
