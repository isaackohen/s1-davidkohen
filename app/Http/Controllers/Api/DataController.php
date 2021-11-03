<?php

namespace App\Http\Controllers\Api;

use \Cache;
use App\User;
use App\Providers;
use Carbon\Carbon;
use App\Settings;
use App\Gameslist;
use App\Leaderboard;
use App\Games\Kernel\Game;
use App\Utils\APIResponse;
use App\Currency\Currency;
use App\Game as GameResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Games\Kernel\Module\General\HouseEdgeModule;

class DataController
{
	
	public function latestGames(Request $request) 
	{
		//Disabled
        return [];

		$result = [];
        switch ($request->type) {
            case 'mine':
                $games = GameResult::latest()->where('demo', '!=', true)->where('user', auth('sanctum')->user()->_id)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($request->count)->get()->reverse();
                break;
            case 'all':
                $games = GameResult::latest()->where('demo', '!=', true)->where('user', '!=', null)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($request->count)->get()->reverse();
                break;
            case 'lucky_wins':
                $games = GameResult::latest()->where('multiplier', '>=', 10)->where('demo', '!=', true)->where('user', '!=', null)->where('status', 'win')->take($request->count)->get()->reverse();
                break;
            case 'high_rollers':
                $hrResult = [];
                $games = GameResult::latest()->where('demo', '!=', true)->where('user', '!=', null)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($request->count)->get()->reverse();
                foreach($games as $game) {
                    if($game->wager < floatval(\App\Currency\Currency::find($game->currency)->option('high_roller_requirement'))) continue;
                    array_push($hrResult, $game);
                }
                $games = $hrResult;
                break;
        }

        foreach($games as $game) {
            if($game->type === 'external') {
            $getgamename = (\App\Gameslist::where('id', $game->game)->first());
            $image = 'Image/https://cdn.davidkohen.com/i/cdn'.$getgamename->image.'?q=95&mask=ellipse&auto=compress&sharp=10&w=20&h=20&fit=crop&usm=5&fm=png';
            $meta = array('id' => $game->game, 'icon' => $image, 'name' => $getgamename->name, 'category' => array($getgamename->category));
            array_push($result, [
				'game' => $game->toArray(),
				'user' => User::where('_id', $game->user)->first()->toArray(),
				'metadata' => $meta
            ]);

            } else {
            array_push($result, [
				'game' => $game->toArray(),
				'user' => User::where('_id', $game->user)->first()->toArray(),
				'metadata' => Game::find($game->game)->metadata()->toArray()
            ]);
            }
		}
        return APIResponse::success($result);
	}
	
	public function notifications(Request $request) 
	{
		return APIResponse::success(array_merge(\App\GlobalNotification::get()->toArray(), env('APP_DEBUG') && !str_contains(request()->url(), 'localhost') ? [[
            '_id' => '-1',
            'icon' => 'fad fa-exclamation-triangle',
            'text' => 'Debug'
        ]] : []));
	}


    /* public static function cachedAllGames()
    {
        
        $games = [];
        foreach(Game::list() as $game) {
            $houseEdgeModule = new HouseEdgeModule($game, null, null, null);

            array_push($games, [
                'isDisabled' => $game->isDisabled(),
                'isPlaceholder' => $game->metadata()->isPlaceholder(),
                'ext' => false,
                'name' => $game->metadata()->name(),
                'id' => $game->metadata()->id(),
                'icon' => $game->metadata()->icon(),
                'cat' => $game->metadata()->category(),
                'p' => 'inhouse',
                'type' => 'local',
                'houseEdge' => !\App\Modules::get($game, false)->isEnabled($houseEdgeModule) ? null : floatval(\App\Modules::get($game, false)->get($houseEdgeModule, 'house_edge_option'))
            ]);
        }
        
        $thirdpartyGames = Gameslist::cachedList(); 
        foreach($thirdpartyGames as $game) {

            if($game->category === 'live-table' || $game->category === 'live') {
            $gameCategory = 'live';
            } else {
            $gameCategory = $game->category;
            }

            array_push($games, [
                'ext' => true,
                'name' => $game->name,
                'id' => $game->id,
                'icon' => $game->image,
                'cat' => array($gameCategory),
                'p' => $game->provider,
                'type' => 'external'            
			]);
        }
        return $games;
    }
	
	public function games(Request $request) 
	{
		if($request->data == 'index'){
			$gamesCached = Cache::get('cachedCategoryGames:index');  
			if (!$gamesCached) { 
				$gamesCached = self::cachedCategoryGames();
				Cache::put('cachedCategoryGames:index', $gamesCached, Carbon::now()->addHours(4));
			} 
		} else if($request->data == 'category') {
			$name = $request->name ?? null;
			$page = $request->page ?? null;
			$gamesCached = Cache::get('cachedCategoryGames:'.$name.':'.$page);  
			if (!$gamesCached) { 
				$gamesCached = self::cachedCategoryGames($name, $page);
				Cache::put('cachedCategoryGames:'.$name.':'.$page, $gamesCached, Carbon::now()->addHours(4));
			}
		} else {
			$gamesCached = Cache::get('cachedAllGames');
			if (!$gamesCached) { 
				$gamesCached = self::cachedAllGames();
				Cache::put('cachedAllGames', $gamesCached, Carbon::now()->addHours(4));
			} 			
		}
        return APIResponse::success($gamesCached);
	} */
	
   /* public static function cachedCategoryGames($name = null, $page = null)
    {
        $games = [];
        $thirdpartyGames = Gameslist::get();
        $popularGames = Settings::get('category_popular');
        $newGames = Settings::get('category_new');
        $gameshowsGames = Settings::get('category_gameshows');
        $bonusGames = Settings::get('category_bonus');
        $featuredGames = Settings::get('category_featured');
        $cardGames = Settings::get('category_cardgames');
		$tableGames = Settings::get('category_tablegames');

		if(!$name || $name == 'inhouse') {
			foreach(Game::list() as $game) {
				$houseEdgeModule = new HouseEdgeModule($game, null, null, null);

				array_push($games, [
					'isDisabled' => $game->isDisabled(),
					'isPlaceholder' => $game->metadata()->isPlaceholder(),
					'ext' => false,
					'name' => $game->metadata()->name(),
					'id' => $game->metadata()->id(),
					'icon' => $game->metadata()->icon(),
					'cat' => array('inhouse'),
					'p' => 'provablyfair',
					'type' => 'local',
					'houseEdge' => !\App\Modules::get($game, false)->isEnabled($houseEdgeModule) ? null : floatval(\App\Modules::get($game, false)->get($houseEdgeModule, 'house_edge_option'))
				]);
			}
		}
		
        foreach($thirdpartyGames as $game) {
			if(!$name || $name == 'scratchcards') {
				if($game->category === 'scratch-cards') {
					array_push($games, [
						'ext' => true,
						'name' => $game->name,
						'id' => $game->id,
						'icon' => $game->image,
						'cat' => array('scratchcards'),
						'p' => $game->provider,
						'type' => 'external'
					]);
				}
			}
			if(!$name || $name == 'vs') {
				if($game->category === 'virtualsports') {
					array_push($games, [
						'ext' => true,
						'name' => $game->name,
						'id' => $game->id,
						'icon' => $game->image,
						'cat' => array('vs'),
						'p' => $game->provider,
						'type' => 'external'
					]);
				}
			}
			if(!$name || $name == 'popular') {
				if(in_array($game["id"], explode(',', $popularGames))) {
					array_push($games, [
						'ext' => true,
						'name' => $game->name,
						'id' => $game->id,
						'icon' => $game->image,
						'cat' => array('popular'),
						'p' => $game->provider,
						'type' => 'external'
					]);
				}
			}
			if(!$name || $name == 'new') {
				if(in_array($game["id"], explode(',', $newGames))) {
					array_push($games, [
						'ext' => true,
						'name' => $game->name,
						'id' => $game->id,
						'icon' => $game->image,
						'cat' => array('new'),
						'p' => $game->provider,
						'type' => 'external'
					]);
				}
			}
			if($name == 'live') {
				if($game->category === 'live-table' || $game->category === 'live') {
					array_push($games, [
						'ext' => true,
						'name' => $game->name,
						'id' => $game->id,
						'icon' => $game->image,
						'cat' => array('live'),
						'p' => $game->provider,
						'type' => 'external'
					]);
				}
			}
			if($name == 'slots') {
				if($game->category === 'slots') {
					array_push($games, [
						'ext' => true,
						'name' => $game->name,
						'id' => $game->id,
						'icon' => $game->image,
						'cat' => array('slots'),
						'p' => $game->provider,
						'type' => 'external'
					]);
				}
			}
        }
		if($page == null) {
			return $games;
		} else if($page >= 0) {
			$response = [];
			array_push($response, [
				'count' => count($games),
				'page' => $page,
				'category' => $name,
				'games' => array_slice($games, $page * 30, 30),
			]);
			return $response;
		}
    } */
	
	public function providers(Request $request)
	{
		$page = $request->page ?? null; 
		$depth = $request->depth ?? 30;
		$data = Gameslist::optimizedList();
		$providers = [];
		foreach ($data as $c) {
			$providers[$c->p] = [
				'name' => $c->p,
				'img' => Providers::where('provider', $c->p)->first()->img ?? ''
			];
		}
		$response = [];
		$data = array_values($providers);
		array_push($response, [
			'count' => count($data),
			'page' => ($page === null ? 'all' : $page),
			'providers' => $page === null ? $data : (array_slice($data, ($page * $depth), $depth))
		]);
		return APIResponse::success($response);
	}
	
	public function categories()
	{
		$data = Gameslist::optimizedList();
		$categories = [];
		foreach ($data as $c) {
			$categories[$c->cat] = [
				'name' => $c->cat
			];
		}
		$data = array_values($categories);
		return APIResponse::success($data);
	}
	
	public function games(Request $request) 
	{
		$text = $request->text ?? null;
		$provider = $request->provider ?? null;
		$category = $request->category ?? null;
		$subcategory = $request->subcategory ?? null;
		$page = $request->page ?? null; 
		$depth = $request->depth ?? 30;
		$data = [];
		if($subcategory){
			foreach ($subcategory as $sub) {
				$data =  array_merge($data, Gameslist::optimizedList($sub));
				if($category) {
					array_push($category, $sub);
				}
			}	
		} 
		if($category || (!$subcategory && !$category)) {
			$data = array_merge($data, Gameslist::optimizedList());	
		}
		$search = collect($data)->filter(function ($item) use ($text, $provider, $category) {
			if($text && !$provider && !$category) {
				if ((stripos($item->name, $text) !== false) || (stripos($item->p, $text) !== false)) {
					return true;
				}
			}
			if($text && $provider && !$category) {
				foreach ($provider as $prov) {
					if ((stripos($item->name, $text) !== false) && (stripos($item->p, $prov) !== false)) {
						return true;
					}
				}
			}
			if($provider && !$text && !$category) {
				foreach ($provider as $prov) {
					if (stripos($item->p, $prov) !== false) {
						return true;
					}
				}
			}
			if($category && !$text && !$provider) {
				foreach ($category as $cat) {
					if (stripos($item->cat, $cat) !== false) {
						return true;
					}
				}
			}
			if($category && $text && !$provider) {
				foreach ($category as $cat) {
					if ((stripos($item->cat, $cat) !== false) && ((stripos($item->name, $text) !== false) || (stripos($item->p, $text) !== false))) {
						return true;
					}
				}
			}
			if($category && $text && $provider) {
				foreach ($provider as $prov) {
					foreach ($category as $cat) {
						if ((stripos($item->cat, $cat) !== false) && (stripos($item->name, $text) !== false) && (stripos($item->p, $prov) !== false)) {
							return true;
						}
					}
				}
			}
			if($category && $provider && !$text) {
				foreach ($provider as $prov) {
					foreach ($category as $cat) {
						if ((stripos($item->cat, $cat) !== false) && (stripos($item->p, $prov) !== false)) {
							return true;
						}
					}
				}
			}
			if(!$text && !$provider && !$category) {
				return true;
			}
			return false;
		});
		$result = array_values($search->all());
		$response = $result;
		$games = [];
		array_push($games, [
			'count' => count($result),
			'page' => ($page === null ? 'all' : $page),
			'games' => $page === null ? $result : (array_slice($result, ($page * $depth), $depth))
		]);
		$response = $games;
		return APIResponse::success($response);
	}
	
	public function currencies(Request $request) 
	{
		return APIResponse::success(Currency::toCurrencyArray(Currency::all()));
	}
	
	public function leaderboard(Request $request) 
	{
		if($request->currency == 'usd') return APIResponse::success(Leaderboard::getLeaderboardByUsd($request->positions, $request->type, $request->orderBy));
		$currency = Currency::find($request->currency ?? '');
		if(!$currency) return APIResponse::reject(2, 'Invalid currency');
		return APIResponse::success(Leaderboard::getLeaderboard($request->positions, $request->type, $currency, $request->orderBy));
	}
	
	public function gameFind(Request $request) 
	{
		$game = Game::where('id', intval($request->id))->first();
        if($game == null) return APIResponse::reject(1, 'Unknown ID ' . $request->id);
        return APIResponse::success([
            'id' => $game->_id,
            'game' => $game->game
        ]);
	}
	
}
