<?php

namespace App;

use App\Modules;
use App\Settings;
use Carbon\Carbon;
use App\Games\Kernel\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Games\Kernel\Module\General\HouseEdgeModule;

class Gameslist extends Model {

    protected $connection = 'mongodb';
    protected $collection = 'gameslist';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'desc', 'provider', 'd', 'category', 'image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'json'
    ];
	
	public static function optimizedList($details = null) {
		$list = [];
		$games = Gameslist::where('d', '!=', '1')->get();
		if($details === null) {
			$cachedList = Cache::get('listAll');  
			if (!$cachedList) { 
				foreach(Game::list() as $game) {
					$houseEdgeModule = new HouseEdgeModule($game, null, null, null);
					array_push($list, (object) [
						'isDisabled' => $game->isDisabled(),
						'isPlaceholder' => $game->metadata()->isPlaceholder(),
						'ext' => false,
						'name' => $game->metadata()->name(),
						'id' => $game->metadata()->id(),
						'icon' => $game->metadata()->icon(),
						'cat' => 'inhouse',
						'p' => 'provablyfair',
						'type' => 'local',
						'houseEdge' => !Modules::get($game, false)->isEnabled($houseEdgeModule) ? null : floatval(Modules::get($game, false)->get($houseEdgeModule, 'house_edge_option'))
					]);
				}
				
				foreach($games as $game) {
					array_push($list, (object) [
						'ext' => true,
						'name' => $game->name,
						'id' => $game->id,
						'icon' => $game->image,
						'cat' => $game->category,
						'p' => $game->provider,
						'type' => 'external'
					]);
				}
				$cachedList = $list;
				Cache::put('listAll', $list, Carbon::now()->addMinutes(120));
			}
		} else {
			$cachedList = Cache::get('cachedList:'.$details);
			if (!$cachedList) { 
				$var = 'category_'.$details;
				$category = Settings::get($var);
				foreach($games as $game) {
					if(in_array($game["id"], explode(',', $category))) {
						array_push($list, (object) [
							'ext' => true,
							'name' => $game->name,
							'id' => $game->id,
							'icon' => $game->image,
							'cat' => $details,
							'p' => $game->provider,
							'type' => 'external'
						]);
					}
				}
				$cachedList = $list;
				Cache::put('cachedList:'.$details, $list, Carbon::now()->addMinutes(120));
			}
		}
		return $cachedList;
	}

    public static function cachedList() {
        $cachedList = Cache::get('cachedList');  

        if (!$cachedList) { 
            $cachedList = Gameslist::where('d', '!=', '1')->get();
            Cache::put('cachedList', $cachedList, Carbon::now()->addMinutes(120));
        } 

        return $cachedList;
    }
}
