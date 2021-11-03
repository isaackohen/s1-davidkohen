<?php
namespace App\Http\Controllers\Admin;

use App\User;
use App\Game;
use App\Invoice;
use App\Withdraw;
use App\Transaction;
use App\Currency\Currency;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use MongoDB\BSON\Decimal128;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
	
    public function user(Request $request)
    {
		$user = User::where('_id', $request->id)->first();

		$currencies = [];
		foreach (Currency::all() as $currency) {
			$currencies = array_merge($currencies, [
				$currency->id() => [
					'games' => Game::where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->where('user', $user->_id)->where('currency', $currency->id())->count(),
					'wins' => Game::where('demo', '!=', true)->where('status', 'win')->where('user', $user->_id)->where('currency', $currency->id())->count(),
					'losses' => Game::where('demo', '!=', true)->where('status', 'lose')->where('user', $user->_id)->where('currency', $currency->id())->count(),
					'wagered' => Game::where('demo', '!=', true)->where('status', '!=', 'cancelled')->where('user', $user->_id)->where('currency', $currency->id())->sum('wager'),
					'deposited' => Invoice::where('user', $user->_id)->where('currency', $currency->id())->sum('sum'),
					'balance' => $user->balance($currency)->get()
				]
			]);
		}

		return APIResponse::success([
			'user' => $user->makeVisible($user->hidden)->toArray(),
			'games' => Game::where('user', $user->_id)->where('demo', '!=', true)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->count(),
			'wins' => Game::where('demo', '!=', true)->where('status', 'win')->where('user', $user->_id)->count(),
			'losses' => Game::where('demo', '!=', true)->where('status', 'lose')->where('user', $user->_id)->count(),
			'transactions' => Transaction::where('user', $user->_id)->where('demo', '!=', true)->get()->toArray(),
			'gamesArray' => Game::where('demo', '!=', true)->where('user', $user->_id)->get()->toArray(),
			'currencies' => $currencies
		]);
    }
	
	public function users()
    {
		return APIResponse::success(User::where('bot', '!=', true)->get()->toArray());
    }
	
	public function checkDuplicates(Request $request)
    {
		$user = User::where('_id', $request->id)->first();
		if($user->bot) return APIResponse::reject(1, 'Can\'t verify bots');

		return APIResponse::success([
			'user' => $user->makeVisible('register_multiaccount_hash')->makeVisible('login_multiaccount_hash')->toArray(),
			'same_register_hash' => User::where('register_multiaccount_hash', $user->register_multiaccount_hash)->get()->toArray(),
			'same_login_hash' => User::where('login_multiaccount_hash', $user->login_multiaccount_hash)->get()->toArray(),
			'same_register_ip' => User::where('register_ip', $user->register_ip)->get()->toArray(),
			'same_login_ip' => User::where('login_ip', $user->login_ip)->get()->toArray()
		]);
    }
	
	public function ban(Request $request)
    {
		$user = User::where('_id', request('id'))->first();
		(new \App\ActivityLog\BanUnbanLog())->insert(['type' => $user->ban ? 'unban' : 'ban', 'id' => $user->_id]);
		$user->update([
			'ban' => $user->ban ? false : true
		]);
		return APIResponse::success();
    }
	
	public function role(Request $request)
    {
		User::where('_id', request('id'))->update([
			'access' => request('role')
		]);
		return APIResponse::success();
    }
	
	public function balance(Request $request)
    {
		User::where('_id', request('id'))->update([
			request('currency') => new Decimal128(strval(request('balance')))
		]);

		(new \App\ActivityLog\BalanceChangeActivity())->insert(['currency' => request('currency'), 'balance' => request('balance'), 'id' => request('id')]);
		return APIResponse::success();
    }
	
}