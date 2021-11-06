<?php

use App\Http\Controllers\BonusController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ExternalController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\BonusController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\ExternalController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;

Route::prefix('callback')->group(function () {
    Route::post('/nowpayments/withdrawals', [PaymentsController::class, 'withdrawalsNowpaymentsCallback']);
    Route::post('/nowpayments', [PaymentsController::class, 'depositNowpaymentsCallback']);
    Route::post('/chaingateway', [PaymentsController::class, 'depositChaingateway']);
});

Route::prefix('node')->group(function () {
    Route::post('pushBullData', [ExternalController::class, 'pushBullData']);
});

Route::get('walletNotify/{currency}/{txid}', [PaymentsController::class, 'walletNotify']);
Route::get('blockNotify/{currency}/{blockId}', [PaymentsController::class, 'blockNotify']);
Route::post('bitgoWebhook', [PaymentsController::class, 'bitgoWebhook']);
Route::post('paymentStatus', [PaymentsController::class, 'paymentStatus']);

Route::post('leaderboard', [DataController::class, 'leaderboard']);
Route::prefix('data')->group(function () {
    Route::post('/latestGames', [DataController::class, 'latestGames']);
    Route::post('/notifications', [DataController::class, 'notifications']);
    Route::post('/currencies', [DataController::class, 'currencies']);
    Route::post('/games', [DataController::class, 'games']);
    Route::post('/providers', [DataController::class, 'providers']);
    Route::post('/categories', [DataController::class, 'categories']);
});

Route::post('/profile/getUser', [UserController::class, 'getUser']);
Route::get('/callback/telegram/{id}', [UserController::class, 'callbackTelegram']);

Route::prefix('user')->group(function () {
    Route::post('graph', [UserController::class, 'graph']);
    Route::get('games/{id}', [UserController::class, 'games']);
    Route::get('statistics/{id}', [UserController::class, 'statistics']);
    Route::post('markGameAsFavorite', [UserController::class, 'markGameAsFavorite']);
});

Route::middleware('auth:sanctum')->prefix('investment')->group(function () {
    Route::post('history', [UserController::class, 'investmentHistory']);
    Route::post('stats', [UserController::class, 'investmentStats']);
});

Route::middleware('auth:sanctum')->prefix('subscription')->group(function () {
    Route::post('update', [UserController::class, 'investmentStats']);
});

Route::middleware('auth:sanctum')->prefix('game')->group(function () {
    Route::post('find', [DataController::class, 'gameFind']);
});

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::post('affiliates', [UserController::class, 'affiliates']);
    Route::post('find', [UserController::class, 'find']);
    Route::post('ignore', [UserController::class, 'ignore']);
    Route::post('unignore', [UserController::class, 'unignore']);
    Route::post('changePassword', [UserController::class, 'changePassword']);
    Route::post('updateEmail', [UserController::class, 'updateEmail']);
    Route::post('client_seed_change', [UserController::class, 'clientSeedChange']);
    Route::post('name_change', [UserController::class, 'nameChange']);
    Route::post('2fa_validate', [UserController::class, 'twofaValidate']);
    Route::post('2fa_enable', [UserController::class, 'twofaEnable']);
    Route::post('2fa_disable', [UserController::class, 'twofaDisable']);
});

Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::post('mark', [NotificationController::class, 'mark']);
    Route::post('unread', [NotificationController::class, 'unread']);
});

Route::middleware('auth:sanctum')->prefix('settings')->group(function () {
    Route::get('privacy_toggle', [UserController::class, 'privacy_toggle']);
    Route::get('privacy_bets_toggle', [UserController::class, 'privacy_bets_toggle']);
    Route::post('avatar', [UserController::class, 'avatar']);
});

Route::middleware('auth:sanctum')->prefix('wallet')->group(function () {
    Route::post('deposit', [WalletController::class, 'deposit']);
    Route::post('withdraw', [WalletController::class, 'withdraw']);
    Route::post('cancel_withdraw', [WalletController::class, 'cancelWithdraw']);
    Route::prefix('history')->group(function () {
        Route::post('deposits', [WalletController::class, 'historyDeposits']);
        Route::post('withdraws', [WalletController::class, 'historyWithdraws']);
    });
    Route::post('getDepositWallet', [WalletController::class, 'getDepositWallet']);
    Route::post('exchange', [WalletController::class, 'exchange']);
});

Route::any('/externalGame/getGamesbyProvider', [ExternalController::class, 'methodGetGamesByProvider']);
Route::any('/externalGame/getUrl', [ExternalController::class, 'methodGetUrl']);
Route::any('/external/bulkbet/balance', [ExternalController::class, 'methodBalance']);
Route::any('/external/bulkbet/bet', [ExternalController::class, 'methodBet']);

Route::post('chat/history', [ChatController::class, 'chatHistory']);

Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::middleware('moderator')->prefix('moderate')->group(function () {
        Route::post('/quiz', [ChatController::class, 'quiz']);
        Route::post('/removeAllFrom', [ChatController::class, 'removeAllFrom']);
        Route::post('/removeMessage', [ChatController::class, 'removeMessage']);
        Route::post('/mute', [ChatController::class, 'mute']);
    });
    Route::post('tip', [ChatController::class, 'tip']);
    Route::post('rain', [ChatController::class, 'rain']);
    Route::post('link_game', [ChatController::class, 'linkGame']);
    Route::post('send', [ChatController::class, 'send']);
    Route::post('sticker', [ChatController::class, 'sticker']);
});

Route::middleware('auth:sanctum')->prefix('promocode')->group(function () {
    Route::post('activate', [BonusController::class, 'activatePromo']);
    Route::post('demo', [BonusController::class, 'demo']);
    Route::post('partner_bonus', [BonusController::class, 'partnerBonus']);
    Route::post('bonus', [BonusController::class, 'bonus']);
    Route::post('vipBonus', [BonusController::class, 'vipBonus']);
    Route::post('telegram_bonus', [BonusController::class, 'telegram']);
    Route::post('slices', [BonusController::class, 'slices']);
    Route::post('depositBonus', [BonusController::class, 'depositBonus']);
    Route::post('depositBonusCancel', [BonusController::class, 'depositBonusCancel']);
    Route::post('bonusStatus', [BonusController::class, 'bonusStatus']);
    Route::post('exchangeBonus', [BonusController::class, 'exchangeBonus']);
});

Route::prefix('game')->group(function () {
    Route::post('play', [GameController::class, 'play']);
    Route::post('turn', [GameController::class, 'turn']);
    Route::post('finish', [GameController::class, 'finish']);
    Route::post('data/{api_id}', [GameController::class, 'data']);
    Route::post('restore', [GameController::class, 'restore']);
    Route::post('info/{id}', [GameController::class, 'info']);
});
