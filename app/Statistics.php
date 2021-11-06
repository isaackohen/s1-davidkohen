<?php

namespace App;

use App\Currency\Currency;
use Jenssegers\Mongodb\Eloquent\Model;

class Statistics extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
	
	protected $connection = 'mongodb';
	protected $collection = 'user_statistics';
		
	protected $fillable = [
		'user',
		'data'
	];
	
	protected $casts = [
        'data' => 'json'
    ];
		
	public static function insert($user, $currency, $wager, $multiplier, $profit) {
		$stats = Statistics::where('user', $user)->first();
		if(!$stats){
			$stats = Statistics::create([
				'user' => $user,
				'data' => []
			]);
		}
		
		$var_bets = 'bets_'.$currency;
		$var_wins = 'wins_'.$currency;
		$var_loss = 'loss_'.$currency;
		$var_wagered = 'wagered_'.$currency;
		$var_profit = 'profit_'.$currency;
		
		$data = $stats->data ?? null;
		if($data == null){
			$keys = array('usd_wager');
			$data = array_fill_keys($keys, '0');
		}
		if (!array_key_exists($var_bets, $data)) {
			$keys = array('games_played', 'usd_wins', $var_bets, $var_wins, $var_loss, $var_wagered, $var_profit);
			$newData = array_fill_keys($keys, '0');
			$data = array_merge($data, $newData);
		}
		$data['usd_wager'] += $wager * Currency::find($currency)->tokenPrice();
		$data['usd_wins'] += $profit * Currency::find($currency)->tokenPrice() ?? 0;
		$data['games_played'] += 1;
		$data[$var_bets] += 1;
		$data[$var_wins] += $profit > 0 ? ($multiplier < 1 ? 0 : 1) : 0;
		$data[$var_loss] += $profit > 0 ? ($multiplier < 1 ? 1 : 0) : 1;
		$data[$var_wagered] += $wager;
		$data[$var_profit] += $profit > 0 ? ($multiplier < 1 ? -($wager) : ($profit)) : -($wager);
		
		$stats->update([
            'data' => $data
        ]);
		
    }
}

