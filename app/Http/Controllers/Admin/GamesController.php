<?php
namespace App\Http\Controllers\Admin;

use \Cache;
use App\DisabledGame;
use App\Utils\APIResponse;
use App\Games\Kernel\Game;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GamesController extends Controller
{
	
    public function toggle(Request $request)
    {
		if(DisabledGame::where('name', request('name'))->first() == null) {
			DisabledGame::create(['name' => request('name')]);
			(new \App\ActivityLog\DisableGameActivity())->insert(['state' => true, 'api_id' => request('name')]);

			Cache::put('disabledGame:'.\request('name'), true);
		} else {
			DisabledGame::where('name', request('name'))->delete();
			(new \App\ActivityLog\DisableGameActivity())->insert(['state' => false, 'api_id' => request('name')]);

			Cache::put('disabledGame:'.\request('name'), false);
		}
		return APIResponse::success();
    }
	
	public function settings()
    {
        return APIResponse::success([
            [
                'name' => 'category_popular',
                'value' => Settings::get('category_popular')
            ],
            [
                'name' => 'category_bonus',
                'value' => Settings::get('category_bonus')
            ],
            [
                'name' => 'category_new',
                'value' => Settings::get('category_new')
            ],
            [
                'name' => 'category_gameshows',
                'value' => Settings::get('category_gameshows')
            ],
            [
                'name' => 'category_cardgames',
                'value' => Settings::get('category_cardgames')
            ],
            [
                'name' => 'category_featured',
                'value' => Settings::get('category_featured')
            ]
        ]);
    }
	
}