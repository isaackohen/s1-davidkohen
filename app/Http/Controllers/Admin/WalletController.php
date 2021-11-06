<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Currency\Currency;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Notifications\WithdrawAccepted;
use App\Notifications\WithdrawDeclined;
use App\User;
use App\Utils\APIResponse;
use App\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use MongoDB\BSON\Decimal128;

class WalletController extends Controller
{
    public function ethereumNativeSendDeposits(Request $request)
    {
        foreach (Invoice::where('currency', 'native_eth')->where('redirected', '!=', true)->get() as $invoice) {
            Currency::find('native_eth')->send(User::where('_id', $invoice->user)->first()->wallet_native_eth, $request->toAddr, floatval((new Decimal128($invoice->sum))->jsonSerialize()['$numberDecimal']));
            $invoice->update(['redirected' => true]);
        }

        return APIResponse::success();
    }

    public function info()
    {
        $withdraws = [];
        $invoices = [];

        foreach (Withdraw::where('status', 0)->get() as $withdraw) {
            $user = User::where('_id', $withdraw->user)->first();
            if (! $user) {
                continue;
            }
            array_push($withdraws, [
                'withdraw' => $withdraw->toArray(),
                'user' => array_merge($user->toArray(), [
                    'vipLevel' => $user->vipLevel(),
                    'balance' => $user->balance(Currency::find($withdraw->currency))->get(),
                ]),
            ]);
        }

        foreach (Invoice::latest()->limit(20)->get() as $invoice) {
            array_push($invoices, [
                'invoice' => $invoice->toArray(),
                'user' => User::where('_id', $invoice->user)->first(),
            ]);
        }

        return APIResponse::success([
            'withdraws' => $withdraws,
            'invoices' => $invoices,
        ]);
    }

    public function infoIgnored()
    {
        $withdraws = [];

        foreach (Withdraw::where('status', 3)->get() as $withdraw) {
            $user = User::where('_id', $withdraw->user)->first();
            array_push($withdraws, [
                'withdraw' => $withdraw->toArray(),
                'user' => array_merge($user->toArray(), [
                    'vipLevel' => $user->vipLevel(),
                    'balance' => $user->balance(Currency::find($withdraw->currency))->get(),
                ]),
            ]);
        }

        return APIResponse::success([
            'withdraws' => $withdraws,
        ]);
    }

    public function accept(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 0) {
            return APIResponse::reject(1, 'Invalid state');
        }

        User::where('_id', $withdraw->user)->first()->notify(new WithdrawAccepted($withdraw));
        $withdraw->update([
            'status' => 1,
        ]);

        return APIResponse::success();
    }

    public function decline(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 0) {
            return APIResponse::reject(1, 'Invalid state');
        }

        $withdraw->update([
            'decline_reason' => request('reason'),
            'status' => 2,
        ]);
        User::where('_id', $withdraw->user)->first()->notify(new WithdrawDeclined($withdraw));
        User::where('_id', $withdraw->user)->first()->balance(Currency::find($withdraw->currency))->add($withdraw->sum);

        return APIResponse::success();
    }

    public function ignore(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 0) {
            return APIResponse::reject(1, 'Invalid state');
        }
        $withdraw->update([
            'status' => 3,
        ]);

        return APIResponse::success();
    }

    public function unignore(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 3) {
            return APIResponse::reject(1, 'Invalid state');
        }
        $withdraw->update([
            'status' => 0,
        ]);

        return APIResponse::success();
    }

    public function autoSetup()
    {
        foreach (Currency::all() as $currency) {
            $currency->setupWallet();
        }

        return APIResponse::success();
    }

    public function transfer()
    {
        try {
            $currency = Currency::find(request('currency'));
            $currency->send($currency->option('transfer_address'), request('address'), floatval(request('amount')));
        } catch (\Exception $e) {
            Log::critical($e);

            return APIResponse::reject(1);
        }

        return APIResponse::success();
    }
}
