<?php

namespace App\Currency\Native;

class Bitcoin extends V17RPCBitcoin
{
    public function id(): string
    {
        return 'native_btc';
    }

    public function walletId(): string
    {
        return 'btc';
    }

    public function name(): string
    {
        return 'BTC';
    }

    public function alias(): string
    {
        return 'bitcoin';
    }

    public function displayName(): string
    {
        return 'Bitcoin';
    }

    public function icon(): string
    {
        return 'btc-icon';
    }

    public function style(): string
    {
        return '#f7931a';
    }

    public function coldWalletBalance(): float
    {
        try {
            return json_decode(file_get_contents('https://sochain.com/api/v2/get_address_balance/BTC/'.$this->option('transfer_address').'/1'))->data->confirmed_balance;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function hotWalletBalance(): float
    {
        try {
            return json_decode(file_get_contents('https://sochain.com/api/v2/get_address_balance/BTC/'.$this->option('withdraw_address').'/1'))->data->confirmed_balance;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
