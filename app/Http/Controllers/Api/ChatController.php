<?php

namespace App\Http\Controllers\Api;

use \Cache;
use App\Game;
use App\Chat;
use App\User;
use App\Settings;
use Carbon\Carbon;
use App\Transaction;
use App\Events\NewQuiz;
use App\Utils\APIResponse;
use App\Events\ChatMessage;
use Illuminate\Http\Request;
use App\Events\ChatRemoveMessages;

class ChatController
{
	
	public function chatHistory(Request $request) 
	{	
		$history = Chat::latest()->limit(35)->where('channel', $request->channel)->where('deleted', '!=', true)->get()->toArray();
		if(Settings::get('quiz_active') === 'true')
			array_unshift($history, [
				"data" => [
					"question" => Settings::get('quiz_question'),
				],
				"type" => "quiz"
			]);
		return APIResponse::success($history);
	}
	
	public function quiz(Request $request) 
	{
		Settings::set('quiz_question', $request->question);
		Settings::set('quiz_answer', $request->answer);
		Settings::set('quiz_active', 'true');
		Settings::set('quiz_created_by', auth('sanctum')->user()->name);

		event(new NewQuiz($request->question));
		return APIResponse::success();
	}
	
	public function removeAllFrom(Request $request) 
	{
		$messages = Chat::where('user', 'like', "%{$request->id}%")->get();
		Chat::where('user', 'like', "%{$request->id}%")->update([
			'deleted' => true
		]);
		$ids = [];
		foreach ($messages as $message) array_push($ids, $message->_id);
		event(new ChatRemoveMessages($ids));
		(new \App\ActivityLog\ChatClearLog())->insert(['type' => 'all', 'id' => $message->user['_id']]);
		return APIResponse::success($ids);
	}
	
	public function removeMessage(Request $request) 
	{
		$message = Chat::where('_id', $request->id)->first();
		$message->update([
			'deleted' => true
		]);
		event(new ChatRemoveMessages([$request->id]));
		(new \App\ActivityLog\ChatClearLog())->insert(['type' => 'one', 'message' => $message->data, 'id' => $message->user['_id']]);
		return APIResponse::success();
	}
	
	public function mute(Request $request) 
	{
		User::where('_id', $request->id)->update([
			'mute' => Carbon::now()->addMinutes($request->minutes)->format('Y-m-d H:i:s')
		]);
		(new \App\ActivityLog\MuteLog())->insert(['id' => $request->id, 'minutes' => $request->minutes]);
		return APIResponse::success();
	}
	
	public function tip(Request $request) 
	{
		$user = User::where('name', 'like', str_replace('.', '', $request->user).'%')->first();
        if($user == null || $user->name === auth('sanctum')->user()->name) return APIResponse::reject(1);
        if(floatval($request->amount) < floatval(auth('sanctum')->user()->clientCurrency()->option('min_tip')) || auth('sanctum')->user()->balance(auth('sanctum')->user()->clientCurrency())->get() < floatval($request->amount)) return APIResponse::reject(2);
        auth('sanctum')->user()->balance(auth('sanctum')->user()->clientCurrency())->subtract(floatval($request->amount), Transaction::builder()->message('Tip to '.$user->_id)->get());
        $user->balance(auth('sanctum')->user()->clientCurrency())->add(floatval($request->amount), Transaction::builder()->message('Tip from '.auth('sanctum')->user()->_id)->get());
        $user->notify(new \App\Notifications\TipNotification(auth('sanctum')->user(), auth('sanctum')->user()->clientCurrency(), number_format(floatval($request->amount), 8, '.', '')));
        if(filter_var($request->public, FILTER_VALIDATE_BOOLEAN)) {
            $message = Chat::create([
                'data' => [
                    'to' => $user->toArray(),
                    'from' => auth('sanctum')->user()->toArray(),
                    'amount' => number_format(floatval($request->amount), 8, '.', ''),
                    'currency' => auth('sanctum')->user()->clientCurrency()->id()
                ],
                'type' => 'tip',
                'vipLevel' => auth('sanctum')->user()->vipLevel(),
                'channel' => $request->channel
            ]);

            event(new ChatMessage($message));
        }
        return APIResponse::success();
	}
	
	public function rain(Request $request) 
	{
		$usersLength = intval($request->users);
        if($usersLength < 5 || $usersLength > 25) return APIResponse::reject(1, 'Invalid users length');
        if(auth('sanctum')->user()->balance(auth('sanctum')->user()->clientCurrency())->get() < floatval($request->amount) || floatval($request->amount) < floatval(auth('sanctum')->user()->clientCurrency()->option('min_rain')) / 3) return APIResponse::reject(2);
        auth('sanctum')->user()->balance(auth('sanctum')->user()->clientCurrency())->subtract(floatval($request->amount), Transaction::builder()->message('Rain')->get());

        $all = \App\ActivityLog\ActivityLogEntry::onlineUsers()->toArray();
        if(count($all) < $usersLength) {
            $a = User::get()->toArray();
            shuffle($a);
            $all += $a;
        }

        shuffle($all);

        $dub = []; $users = [];
        foreach ($all as $user) {
            $user = User::where('_id', $user['_id'])->first();
            if($user['_id'] == auth('sanctum')->user()->_id || $user == null || in_array($user['_id'], $dub)) continue;
            array_push($dub, $user['_id']);
            array_push($users, $user);
        }

        $users = array_slice($users, 0, $usersLength);
        $result = [];

        foreach ($users as $user) {
            $user->balance(auth('sanctum')->user()->clientCurrency())->add(floatval($request->amount) / $usersLength, Transaction::builder()->message('Rain')->get());
            array_push($result, $user->toArray());
        }

        $message = Chat::create([
            'data' => [
                'users' => $result,
                'reward' => floatval($request->amount) / $usersLength,
                'currency' => auth('sanctum')->user()->clientCurrency()->id(),
                'from' => auth('sanctum')->user()->toArray()
            ],
            'type' => 'rain',
            'vipLevel' => auth('sanctum')->user()->vipLevel(),
            'channel' => $request->channel
        ]);

        event(new ChatMessage($message));
        return APIResponse::success();
	}
	
	public function linkGame(Request $request) 
	{
		if(auth('sanctum')->user()->mute != null && !auth('sanctum')->user()->mute->isPast()) return APIResponse::reject(2, 'Banned');
        $game = Game::where('_id', $request->id)->first();
        if($game == null) return APIResponse::reject(1, 'Invalid game id');
        if($game->status === 'in-progress' || $game->status === 'cancelled') return APIResponse::reject(2, 'Tried to link unfinished extended game');
		if($game->type === "external") {
            $getgamename = (\App\Gameslist::where('id', $game->game)->first());
            $image = 'Image/https://cdn.davidkohen.com/i/cdn'.$getgamename->image.'?q=95&mask=ellipse&auto=compress&sharp=10&w=20&h=20&fit=crop&usm=5&fm=png';
			$info = $game;
			$info->game = $getgamename->name;
			$data = array_merge($info->toArray(), ['icon' => $image]);
		} else {
			$data = array_merge($game->toArray(), ['icon' => \App\Games\Kernel\Game::find($game->game)->metadata()->icon()]);
		}

        $message = Chat::create([
            'user' => auth('sanctum')->user()->toArray(),
            'vipLevel' => auth('sanctum')->user()->vipLevel(),
            'data' => $data,
            'type' => 'game_link',
            'channel' => $request->channel
        ]);

        event(new ChatMessage($message));
        return APIResponse::success([]);
	}
	
    public function send(Request $request)
    {
        $last3Hours = \Carbon\Carbon::now()->subMinutes(10);
        $user = User::where('_id', auth()->user()->id)->first();
        if(strlen($request->message) < 1 || strlen($request->message) > 100) return reject(1, 'Message is too short or long');
        if(auth()->user()->mute != null && !auth()->user()->mute->isPast()) return reject(2, 'User is banned');
        if($user->created_at >= $last3Hours) return reject(2, 'User is banned');

        $message = Chat::create([
            'user' => auth()->user()->toArray(),
            'user_id' => auth()->user()->_id,
            'vipLevel' => auth()->user()->vipLevel(),
            'data' => mb_substr($request->message, 0, 400),
            'type' => 'message',
            'channel' => $request->channel
        ]);

        event(new ChatMessage($message));
		
        if(Settings::get('quiz_active') === 'true') {
            $sanitize = function ($input) {
                return mb_strtolower(preg_replace("/[^A-Za-zА-Яа-я0-9\-]/u", '', $input));
            };

            if($sanitize($request->message) === $sanitize(Settings::get('quiz_answer'))) {
                Settings::set('quiz_active', false);
                auth()->user()->balance(auth()->user()->clientCurrency())->add(floatval(auth()->user()->clientCurrency()->option('quiz')), Transaction::builder()->message('Quiz')->get());
                event(new \App\Events\QuizAnswered(auth()->user(), Settings::get('quiz_question'), Settings::get('quiz_answer')));
            }
        }
		
        return APIResponse::success([]);
    }
	
	public function sticker(Request $request)
    {
        $last3Hours = Carbon::now()->subMinutes(10);
        $user = User::where('_id', auth()->user()->id)->first();
        if(auth()->user()->mute != null && !auth()->user()->mute->isPast()) return reject(2, 'User is banned');
        if($user->created_at >= $last3Hours) return reject(2, 'User is banned');

        $message = Chat::create([
            'user' => auth()->user()->toArray(),
            'vipLevel' => auth()->user()->vipLevel(),
            'data' => $request->url,
            'type' => 'gif',
            'channel' => $request->channel
        ]);
        event(new ChatMessage($message));
		
        return APIResponse::success([]);
    }
	
}
