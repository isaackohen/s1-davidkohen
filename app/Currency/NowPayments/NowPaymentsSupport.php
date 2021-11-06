<?php

namespace App\Currency\NowPayments;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use App\Invoice;
use App\Settings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

abstract class NowPaymentsSupport extends Currency
{
    private function balance($account): float
    {
        return -1;
    }

    public function coldWalletBalance(): float
    {
        return -1;
    }

    public function hotWalletBalance(): float
    {
        return -1;
    }

    public function isRunning(): bool
    {
        try {
            $json = json_decode(file_get_contents('http://api.nowpayments.io/v1/status'), true);

            return $json['message'] === 'OK';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function send(string $from, string $to, float $sum)
    {
    }

    public function process(string $wallet = null)
    {
    }

    public function processBlock($blockId)
    {
    }

    public function newWalletAddress($accountName = null): string
    {
        try {
            $min_deposit_str = 'nowpayments_min_'.$this->nowpayments();
            $min_deposit_usd = round(Settings::where('name', $min_deposit_str)->first()->value + 2, 2);
            $invoice = Invoice::create([
                'currency' => $this->id(),
                'user' => auth('sanctum')->user()->_id,
                'status' => 0,
                'hash' => Hash::make(12),
            ]);
            $ipn = env('APP_URL').'/api/callback/nowpayments';
            $curl = curl_init();
            curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.nowpayments.io/v1/payment',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
				 "price_amount": "'.$min_deposit_usd.'",
				 "price_currency": "usd",
				 "pay_currency": "'.$this->nowpayments().'",
				 "ipn_callback_url": "'.$ipn.'",
				 "order_id": "'.$invoice->_id.'",
				 "order_description": "'.$invoice->hash.'"
			}',
            CURLOPT_HTTPHEADER => [
               'x-api-key: '.env('NOWPAYMENTS_ID').'',
               'Content-Type: application/json',
            ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $responseResult = json_decode($response);
            if ($responseResult->pay_currency == 'xrp' || $responseResult->pay_currency == 'bnb') {
                $invoice->update([
                    'payid' => $responseResult->payin_extra_id,
                    'ledger' => $responseResult->pay_address,
                    'min_deposit' => $responseResult->pay_amount,
                    'min_deposit_usd' => $responseResult->price_amount,
                ]);
            } else {
                $invoice->update([
                    'ledger' => $responseResult->pay_address,
                    'min_deposit' => $responseResult->pay_amount,
                    'payid' => $responseResult->payment_id,
                    'min_deposit_usd' => $responseResult->price_amount,
                ]);
            }

            return $invoice->ledger;
        } catch (\Exception $e) {
            Log::critical($e);

            return 'Error';
        }
    }

    public function setupWallet(): ?string
    {
        return null;
    }

    protected function options(): array
    {
        return [];
    }

    public function depositmethod(): string
    {
        return 'nowpayments';
    }

    public function withdrawmethod(): string
    {
        return 'nowpayments';
    }
}
