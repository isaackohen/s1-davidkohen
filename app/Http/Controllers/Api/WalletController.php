<?php

namespace App\Http\Controllers\Api;

use App\Currency\Currency;
use App\Invoice;
use App\Settings;
use App\Transaction;
use App\Utils\APIResponse;
use App\Withdraw;
use Cache;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class WalletController
{
    public function deposit(Request $request)
    {
        $minimal = floatval(Currency::find('local_rub')->option('min_deposit'));
        if (auth('sanctum')->user()->vipLevel() >= 4) {
            $minimal /= 2;
        }

        if (floatval($request->sum) < $minimal) {
            return APIResponse::reject(1, 'Invalid deposit value');
        }
        $aggregator = Aggregator::find($request->aggregator);
        if ($aggregator == null) {
            return APIResponse::reject(2, 'Invalid aggregator');
        }

        $invoice = Invoice::create([
            'method' => $request->type,
            'sum' => floatval($request->sum),
            'user' => auth('sanctum')->user()->_id,
            'aggregator' => $aggregator->id(),
            'currency' => 'local_rub',
            'status' => 0,
        ]);

        return APIResponse::success([
            'url' => $aggregator->invoice($invoice),
        ]);
    }

    public function historyDeposits(Request $request)
    {
        return APIResponse::success(Invoice::where('user', auth('sanctum')->user()->_id)->where('status', '>=', 1)->latest()->get()->toArray());
    }

    public function historyWithdraws(Request $request)
    {
        return APIResponse::success(Withdraw::where('user', auth('sanctum')->user()->_id)->latest()->get()->toArray());
    }

    public function getDepositWallet(Request $request)
    {
        $currency = Currency::find($request->currency);
        $wallet = auth('sanctum')->user()->depositWallet($currency);
        if ($currency == null || ! $currency->isRunning() || $wallet === 'Error') {
            return APIResponse::reject(1);
        }
        $invoice = Invoice::where('ledger', $wallet)->where('updated_at', '>=', Carbon::parse(Carbon::today()->toDateTimeString()))->where('status', 0)->first();
        if (! $invoice) {
            return APIResponse::reject(1);
        }
        $payid = $invoice->payid;
        $min_deposit = $invoice->min_deposit;
        $min_deposit_usd = $invoice->min_deposit_usd;

        return APIResponse::success([
            'currency' => $request->currency,
            'wallet' => $wallet,
            'payid' => $payid,
            'min_deposit' => $min_deposit,
            'min_deposit_usd' => $min_deposit_usd,
        ]);
    }

    public function withdraw(Request $request)
    {
        if (! auth('sanctum')->user()->validate2FA(false)) {
            return APIResponse::invalid2FASession();
        }
        auth('sanctum')->user()->reset2FAOneTimeToken();
        $currency = Currency::find($request->currency);
        $manualTrigger = floatval($currency->option('withdraw_manual_trigger'));
        $manualTrigger = number_format(($manualTrigger), 7, '.', '');
        if ($request->sum < floatval($currency->option('withdraw')) + floatval($currency->option('fee'))) {
            return APIResponse::reject(1, 'Invalid withdraw value');
        }
        if (auth('sanctum')->user()->balance($currency)->get() < $request->sum + floatval($currency->option('fee'))) {
            return APIResponse::reject(2, 'Not enough balance');
        }
        if (Withdraw::where('user', auth('sanctum')->user()->_id)->where('status', 0)->count() > 0) {
            return APIResponse::reject(3, 'Moderation is still in process');
        }
        if (auth('sanctum')->user()->access == 'moderator') {
            return APIResponse::reject(1, 'Not available');
        }

        auth('sanctum')->user()->balance($currency)->subtract($request->sum + floatval($currency->option('fee')), Transaction::builder()->message('Withdraw')->get());

        if (auth('sanctum')->user()->balance($currency)->get() + Withdraw::where('status', 0)->where('user', auth('sanctum')->user()->_id)->where('currency', $currency->id())->sum('sum') > $manualTrigger) {
            $withdraw = Withdraw::create([
                'user' => auth('sanctum')->user()->_id,
                'sum' => $request->sum,
                'currency' => $currency->id(),
                'address' => $request->wallet,
                'status' => 0,
                'auto' => false,
            ]);
        } else {
            $withdraw = Withdraw::create([
                'user' => auth('sanctum')->user()->_id,
                'sum' => $request->sum,
                'currency' => $currency->id(),
                'address' => $request->wallet,
                'status' => 0,
                'auto' => true,
            ]);

            if ($currency->withdrawmethod() === 'chaingateway') {
                if ($currency->option('contract_address') === '0') {
                    $url = 'https://eu.bsc.chaingateway.io/v1/sendBinancecoin';
                } else {
                    $url = 'https://eu.bsc.chaingateway.io/v1/sendToken';
                }
                $apikey = env('CHAINGATEWAY_APIKEY'); // API Key in your account panel
                $password = env('CHAINGATEWAY_PASSWORD'); // Chaingateway password

                // Define function endpoint
                $ch = curl_init($url);

                // Setup request to send json via POST. This is where all parameters should be entered.

                if ($currency->option('contract_address') === '0') {
                    $payload = json_encode(['from' => $currency->option('withdraw_address'), 'to' => $request->wallet, 'password' => $password, 'amount' => $requestAmount]);
                } else {
                    $payload = json_encode(['contractaddress' => $currency->option('contract_address'), 'from' => $currency->option('withdraw_address'), 'to' => $request->wallet, 'password' => $password, 'amount' => $requestAmount]);
                }

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: '.$apikey]);

                // Return response instead of printing.
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Send request.
                $result = curl_exec($ch);
                curl_close($ch);

                // Decode the received JSON string
                $resultdecoded = json_decode($result, true);

                $okresponse = $resultdecoded['ok'];
                if ($okresponse === true) {
                    $withdraw->update(['status' => (int) 1]);
                    $withdraw->update(['hash' => $resultdecoded['txid']]);

                    return APIResponse::success();
                }
            }

            return APIResponse::success();
        }
    }

    public function cancelWithdraw(Request $request)
    {
        $withdraw = Withdraw::where('_id', $request->id)->where('user', auth('sanctum')->user()->_id)->where('status', 0)->first();
        if ($withdraw == null) {
            return APIResponse::reject(1, 'Hacking attempt');
        }
        if ($withdraw->auto) {
            return APIResponse::reject(2, 'Auto-withdrawals cannot be cancelled');
        }
        $withdraw->update([
            'status' => 4,
        ]);
        auth('sanctum')->user()->balance(Currency::find($withdraw->currency))->add($withdraw->sum, Transaction::builder()->message('Withdraw cancellation')->get());

        return APIResponse::success();
    }

    public function exchange(Request $request)
    {
        if (! auth('sanctum')->user()->validate2FA(false)) {
            return APIResponse::invalid2FASession();
        }
        auth('sanctum')->user()->reset2FAOneTimeToken();
        $currencyFrom = Currency::find($request->from);
        $currencyTo = Currency::find($request->to);
        if ($currencyFrom == $currencyTo) {
            APIResponse::reject(2, 'Abuse both currencies');
        }
        if (! $currencyFrom || ! $currencyTo) {
            return APIResponse::reject(2, 'Invalid currency');
        }
        $amount = floatval($request->amount);
        if ($amount == 0) {
            return APIResponse::reject(1, 'Invalid amount');
        }
        if (auth()->user()->balance($currencyFrom)->get() < $amount) {
            return APIResponse::reject(1, 'Invalid amount');
        }
        auth()->user()->balance($currencyFrom)->subtract($amount, Transaction::builder()->message('Exchange')->get());
        auth()->user()->balance($currencyTo)->add($currencyTo->convertUSDToToken($currencyFrom->convertTokenToUSD($amount)), Transaction::builder()->message('Exchange')->get());

        return APIResponse::success();
    }
}
