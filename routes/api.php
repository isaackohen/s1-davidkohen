<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\PaymentsController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\ExternalController;

Route::prefix('callback')->group(function() {
	Route::post('/nowpayments/withdrawals', 'PaymentsController@withdrawalsNowpaymentsCallback');
	Route::post('/nowpayments', 'PaymentsController@depositNowpaymentsCallback');
	Route::post('/chaingateway', 'PaymentsController@depositChaingateway');
});

Route::prefix('node')->group(function() {
	Route::post('pushBullData', 'ExternalController@pushBullData');
});

Route::get('walletNotify/{currency}/{txid}', 'PaymentsController@walletNotify');
Route::get('blockNotify/{currency}/{blockId}', 'PaymentsController@blockNotify');
Route::post('bitgoWebhook', 'PaymentsController@bitgoWebhook');
Route::post('paymentStatus', 'PaymentsController@paymentStatus');

Route::post('leaderboard', 'DataController@leaderboard');
Route::prefix('data')->group(function() {
	Route::post('/latestGames', 'DataController@latestGames');
	Route::post('/notifications', 'DataController@notifications');
	Route::post('/currencies', 'DataController@currencies');
	Route::post('/games', 'DataController@games');
	Route::post('/providers', 'DataController@providers');
	Route::post('/categories', 'DataController@categories');
});

Route::post('/profile/getUser', 'UserController@getUser');
Route::get('/callback/telegram/{id}', 'UserController@callbackTelegram');

Route::prefix('user')->group(function() {
	Route::post('graph', 'UserController@graph');
	Route::get('games/{id}', 'UserController@games');
	Route::get('statistics/{id}', 'UserController@statistics');
	Route::post('markGameAsFavorite', 'UserController@markGameAsFavorite');
});

Route::middleware('auth:sanctum')->prefix('investment')->group(function() {
    Route::post('history', 'UserController@investmentHistory');
    Route::post('stats', 'UserController@investmentStats');
});

Route::middleware('auth:sanctum')->prefix('subscription')->group(function() {
	Route::post('update', 'UserController@investmentStats');
});

Route::middleware('auth:sanctum')->prefix('game')->group(function() {
	Route::post('find', 'DataController@gameFind');
});

Route::middleware('auth:sanctum')->prefix('user')->group(function() {
	Route::post('affiliates', 'UserController@affiliates');
	Route::post('find', 'UserController@find');
	Route::post('ignore', 'UserController@ignore');
	Route::post('unignore', 'UserController@unignore');
	Route::post('changePassword', 'UserController@changePassword');
	Route::post('updateEmail', 'UserController@updateEmail');
	Route::post('client_seed_change', 'UserController@clientSeedChange');
	Route::post('name_change', 'UserController@nameChange');
	Route::post('2fa_validate', 'UserController@twofaValidate');
	Route::post('2fa_enable', 'UserController@twofaEnable');
	Route::post('2fa_disable', 'UserController@twofaDisable');
});

Route::middleware('auth:sanctum')->prefix('notifications')->group(function() {
	Route::post('mark', 'NotificationController@mark');
	Route::post('unread', 'NotificationController@unread');
});

Route::middleware('auth:sanctum')->prefix('settings')->group(function() {	
	Route::get('privacy_toggle', 'UserController@privacy_toggle');
	Route::get('privacy_bets_toggle', 'UserController@privacy_bets_toggle');
	Route::post('avatar', 'UserController@avatar');
});

Route::middleware('auth:sanctum')->prefix('wallet')->group(function() {
	Route::post('deposit', 'WalletController@deposit');
	Route::post('withdraw', 'WalletController@withdraw');
	Route::post('cancel_withdraw', 'WalletController@cancelWithdraw');
    Route::prefix('history')->group(function() {
		Route::post('deposits', 'WalletController@historyDeposits');
		Route::post('withdraws', 'WalletController@historyWithdraws');
    });
	Route::post('getDepositWallet', 'WalletController@getDepositWallet');
	Route::post('exchange', 'WalletController@exchange');
});

Route::any('/externalGame/getGamesbyProvider', 'ExternalController@methodGetGamesByProvider');
Route::any('/externalGame/getUrl', 'ExternalController@methodGetUrl');
Route::any('/external/bulkbet/balance', 'ExternalController@methodBalance');
Route::any('/external/bulkbet/bet', 'ExternalController@methodBet');

Route::post('chat/history', 'ChatController@chatHistory');

Route::middleware('auth:sanctum')->prefix('chat')->group(function() {
    Route::middleware('moderator')->prefix('moderate')->group(function() {
		Route::post('/quiz', 'ChatController@quiz');
		Route::post('/removeAllFrom', 'ChatController@removeAllFrom');
		Route::post('/removeMessage', 'ChatController@removeMessage');
		Route::post('/mute', 'ChatController@mute');
    });
	Route::post('tip', 'ChatController@tip');
	Route::post('rain', 'ChatController@rain');
	Route::post('link_game', 'ChatController@linkGame');
	Route::post('send', 'ChatController@send');
	Route::post('sticker', 'ChatController@sticker');
});

Route::middleware('auth:sanctum')->prefix('promocode')->group(function() {
	Route::post('activate', 'BonusController@activatePromo');
	Route::post('demo', 'BonusController@demo');
	Route::post('partner_bonus', 'BonusController@partnerBonus');
	Route::post('bonus', 'BonusController@bonus');
	Route::post('vipBonus', 'BonusController@vipBonus');
	Route::post('telegram_bonus', 'BonusController@telegram');
	Route::post('slices', 'BonusController@slices');
	Route::post('depositBonus', 'BonusController@depositBonus');
	Route::post('depositBonusCancel', 'BonusController@depositBonusCancel');
	Route::post('bonusStatus', 'BonusController@bonusStatus');
	Route::post('exchangeBonus', 'BonusController@exchangeBonus');
});

Route::prefix('game')->group(function() {
    Route::post('play', 'GameController@play');
    Route::post('turn', 'GameController@turn');
    Route::post('finish', 'GameController@finish');
    Route::post('data/{api_id}', 'GameController@data');
	Route::post('restore', 'GameController@restore');
    Route::post('info/{id}', 'GameController@info');
});
