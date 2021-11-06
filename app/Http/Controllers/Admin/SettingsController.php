<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Settings;
use App\Utils\APIResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function get()
    {
        return APIResponse::success([
            'mutable' => Settings::where('internal', '!=', true)->where('hidden', '!=', true)->where('cat', '=', null)->get()->toArray(),
            'immutable' => Settings::where('internal', true)->where('hidden', '!=', true)->where('cat', '=', null)->get()->toArray(),
            'bonus' => Settings::where('internal', '!=', true)->where('cat', 'bonus')->where('hidden', '!=', true)->get()->toArray(),
            'global' => [
                [
                    '_id' => '1',
                    'cat' => 'global',
                    'name' => 'nowpayments_apikey',
                    'value' => env('NOWPAYMENTS_ID'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
                [
                    '_id' => '2',
                    'cat' => 'global',
                    'name' => 'bulkbet_apikey',
                    'value' => env('API_KEY'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
                [
                    '_id' => '3',
                    'cat' => 'global',
                    'name' => 'chaingateway_apikey',
                    'value' => env('CHAINGATEWAY_APIKEY'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
                [
                    '_id' => '4',
                    'cat' => 'global',
                    'name' => 'chaingateway_password',
                    'value' => env('CHAINGATEWAY_PASSWORD'),
                    'hidden' => false,
                    'internal' => true,
                    'updated_at' => '2021-10-16T16:22:48.008000Z',
                    'created_at' => '2021-02-13T20:12:36.772000Z',
                ],
            ],
        ]);
    }

    public function create(Request $request)
    {
        Settings::create(['name' => request('key'), 'cat' => request('cat'), 'description' => request('description'), 'value' => null]);

        return APIResponse::success();
    }

    public function edit(Request $request)
    {
        Settings::where('name', request('key'))->first()->update([
            'value' => request('value') === 'null' ? null : request('value'),
        ]);

        return APIResponse::success();
    }

    public function remove(Request $request)
    {
        Settings::where('name', request('key'))->delete();

        return APIResponse::success();
    }

    public function botSettings(Request $request)
    {
        return APIResponse::success([
            [
                'name' => 'create_new_bot_every_ms',
                'value' => Settings::get('create_new_bot_every_ms', 20000, true),
            ],
            [
                'name' => 'hidden_bets_probability',
                'value' => Settings::get('hidden_bets_probability', 20, true),
            ],
            [
                'name' => 'hidden_profile_probability',
                'value' => Settings::get('hidden_profile_probability', 20, true),
            ],
            [
                'name' => 'min_amount_of_games_from_one_bot',
                'value' => Settings::get('min_amount_of_games_from_one_bot', 20, true),
            ],
            [
                'name' => 'max_amount_of_games_from_one_bot',
                'value' => Settings::get('max_amount_of_games_from_one_bot', 50, true),
            ],
            [
                'name' => 'min_delay_between_games_from_one_bot_ms',
                'value' => Settings::get('min_delay_between_games_from_one_bot_ms', 1000, true),
            ],
            [
                'name' => 'max_delay_between_games_from_one_bot_ms',
                'value' => Settings::get('max_delay_between_games_from_one_bot_ms', 5000, true),
            ],
        ]);
    }

    public function startBot()
    {
        dispatch(new \App\Jobs\Bot\BotScheduler());

        return APIResponse::success();
    }
}
