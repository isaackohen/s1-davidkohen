<?php

namespace App\Http\Controllers\Api;

use \Cache;
use App\User;
use App\Game;
use Carbon\Carbon;
use App\Gameslist;
use App\Statistics;
use App\Transaction;
use App\Leaderboard;
use App\Utils\APIResponse;
use App\Currency\Currency;
use Illuminate\Http\Request;
use App\Events\LiveFeedGame;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Settings;

class ExternalController
{
	
	public function methodBalance(Request $request) 
	{
		$user = User::where('_id', $_GET["playerid"])->first();
        $currency = Currency::find($_GET["currency"]);
        $getBalance = $user->balance($currency)->get();
        $getBalanceUSD = intval($currency->convertTokenToUSD($getBalance) * 100);

        $responsePayload = array('status' => 'ok', 'result' => array('balance' => $getBalanceUSD, 'freegames' => 0));

        echo json_encode($responsePayload);
    }
	
	public function methodBet(Request $request) 
	{
		Log::alert($request->fullUrl());
        $user = User::where('_id', $_GET["playerid"])->first();
        $currency = Currency::find($_GET["currency"]);
        $win = $_GET["win"];
        $bet = $_GET["bet"];
        $final = $_GET["final"];
        $gameid = $_GET["gameid"];
        $roundid = $request["roundid"];

        if($bet > 0) {
            $betFloat = $_GET["bet"] / 100;
            $gameData = json_encode($request);
            $bet = number_format($currency->convertUSDToToken($betFloat), 8, '.', '');
            $user->balance($currency)->subtract(floatval($bet), Transaction::builder()->meta($roundid)->game($gameid)->get());
        }

        if($win > 0) {
            $winFloat = $_GET["win"] / 100;
            $win = number_format($currency->convertUSDToToken($winFloat), 8, '.', '');
            $user->balance($currency)->add(floatval($win), Transaction::builder()->meta($roundid)->game($gameid)->get());
        }


        if($final === '1') {
            $wager = Transaction::where("meta", "=", $roundid)->first()->amount;
            $wagerTrimmed = floatval(trim($wager, '-'));
            //Log::critical($wagerTrimmed);
            $win = floatval($win);

            $status = 'lose';
            if($win > $wagerTrimmed) $status = 'win';
            if($wagerTrimmed < 0.0000001) {
                $multi = 0;
            } else {
                $multi = (float) ($win / $wagerTrimmed);
            }
			$profit = (($win - $wagerTrimmed) <= 0) ? 0 : ($win - $wagerTrimmed);

            $game = Game::create([
                'id' => DB::table('games')->count() + 1,
                'user' => $user->_id,
                'game' => $gameid,
                'wager' => $wagerTrimmed,
                'multiplier' => $multi,
                'status' => $status,
                'profit' => $profit,
                'server_seed' => '-1',
                'client_seed' => '-1',
                'nonce' => '-1',
                'data' => [],
                'type' => 'external',
                'currency' => $currency->id()
            ]);

            event(new LiveFeedGame($game, '1'));
            Leaderboard::insert($game);
			Statistics::insert(
				$game->user, 
				$game->currency, 
				$game->wager, 
				$game->multiplier, 
				$game->profit
			);
			if ($user->vipLevel() > 0 && ($user->weekly_bonus ?? 0) < 100 && ((Settings::get('weekly_bonus_minbet') / Currency::find(Settings::get('bonus_currency'))->tokenPrice()) ?? 1) <= $game->wager) $user->update(['weekly_bonus' => ($user->weekly_bonus ?? 0) + 0.1]);
        }
 
        $getBalance = $user->balance($currency)->get();
        $getBalanceUSD = intval($currency->convertTokenToUSD($getBalance) * 100);
        
        $responsePayload = array('status' => 'ok', 'result' => array('balance' => $getBalanceUSD, 'freegames' => 0));

        echo json_encode($responsePayload);
    }
	
    public function methodGetGamesByProvider(Request $request)
    {
        $amountTake = '10';
        $gameId = $request->id;
        $thirdpartyGames = Gameslist::cachedList()->where('id', '=', $gameId)->first()->provider;
        $thirdpartyGames = Gameslist::cachedList()->where('provider', '=', $thirdpartyGames)->take($amountTake);

        $games = [];

        foreach($thirdpartyGames as $game) {
        array_push($games, [
            'ext' => true,
            'name' => $game->name,
            'id' => $game->id,
            'icon' => $game->image,
            'cat' => array($game->category),
            'p' => $game->provider,
            'type' => 'external'
        ]);
        }
        return $games;
    }

	public function methodGetUrl(Request $request) 
	{

        try {
			if(auth('sanctum')->guest()) {
				$mode = 'demo';
				$currencyId = 'usd';
			} else {
				$mode = 'real';
				$userId = auth('sanctum')->user()->id;
				$currencyId = auth('sanctum')->user()->clientCurrency()->id();
			}
			$apikey = env('API_KEY');
			$url = "https://api.dk.games/v2/createSession?apikey=".$apikey."&userid=".$userId."-".$currencyId."&game=".$request->id."&mode=".$mode;
			$result = file_get_contents($url);
			$decodeArray = json_decode($result, true);

			$gameslist = (Gameslist::where('id', $request->id)->first());
			return APIResponse::success([
				'url' => $decodeArray['url'],
				'id' => $gameslist['id'],
				'name' => $gameslist['name'],
				'provider' => $gameslist['provider']
			]);
        } catch (\Exception $e) {
			return APIResponse::success([
				'status' => 'error'
			]);
        }

    }
}
