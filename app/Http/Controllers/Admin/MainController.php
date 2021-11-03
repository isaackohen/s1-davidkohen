<?php
namespace App\Http\Controllers\Admin;

use App;
use App\User;
use App\Withdraw;
use Carbon\Carbon;
use App\AdminActivity;
use Illuminate\View\View;
use App\Utils\APIResponse;
use App\ActivityLog\ActivityLogEntry;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
	
    public function main()
    {
        return view("layouts.admin");
    }
	
	public function info()
    {
		$get = function($type) {
			$total = App::make(\Arcanedev\LogViewer\Contracts\LogViewer::class)->total($type);
			return $total > 999 ? 999 : $total;
		};

		return APIResponse::success([
			'withdraws' => Withdraw::where('status', 0)->count(),
			'version' => json_decode(file_get_contents(base_path('package.json')))->version,
			'logs' => [
				'critical' => $get('critical'),
				'error' => $get('error')
			]
		]);
    }
	
	public function games()
    {
        return APIResponse::success([
            'games' => view('admin.games')->toHtml()
        ]);
    }
	
	public function analytics()
    {
        return APIResponse::success([
            'analytics' => view('admin.analytics')->toHtml()
        ]);
    }
	
	public function deposits()
    {
        return APIResponse::success([
            'dashboard' => view('admin.dashboard')->toHtml()
        ]);
    }
		
	public function activity()
    {
		$activity = [];
		foreach(AdminActivity::latest()->get()->reverse() as $log) {
			if (ActivityLogEntry::find($log->type) == null) continue;
			$user = User::where('_id', $log->user)->first();
			if(!$user) continue;

			array_push($activity, [
				'user' => $user->toArray(),
				'entry' => $log->toArray(),
				'time' => Carbon::parse($log->time)->diffForHumans(),
				'html' => ActivityLogEntry::find($log->type)->display($log)
			]);
		}

		return APIResponse::success($activity);
    }
	
}