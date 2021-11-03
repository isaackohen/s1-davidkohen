<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Promocode;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PromocodeController extends Controller
{
	
    public function get()
    {
        return APIResponse::success(Promocode::get()->toArray());
	}
	
	public function create(Request $request)
    {
		request()->validate([
            'code' => 'required',
            'usages' => 'required',
            'expires' => 'required',
            'sum' => 'required',
            'currency' => 'required'
        ]);

        Promocode::create([
            'code' => request('code') === '%random%' ? Promocode::generate() : request('code'),
            'currency' => request('currency'),
            'used' => [],
            'sum' => floatval(request('sum')),
            'usages' => request('usages') === '%infinite%' ? -1 : intval(request('usages')),
            'times_used' => 0,
            'expires' => request('expires') === '%unlimited%' ? Carbon::minValue() : Carbon::createFromFormat('d-m-Y H:i', request()->get('expires'))
        ]);
        return APIResponse::success();
	}
	
	public function remove()
    {
		Promocode::where('_id', request()->get('id'))->delete();
        return APIResponse::success();
	}
	
	public function removeInactive(Request $request)
    {
		foreach(Promocode::get() as $promocode) {
            if(($promocode->expires->timestamp != Carbon::minValue()->timestamp && $promocode->expires->isPast())
                || ($promocode->usages != -1 && $promocode->times_used >= $promocode->usages)) $promocode->delete();
        }
        return APIResponse::success();
	}
	
}