<?php

namespace App\Currency\NowPayments;

class Dogecoin extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_doge';
    }

    public function walletId(): string
    {
        return 'doge';
    }

    public function name(): string
    {
        return 'DOGE';
    }

    public function alias(): string
    {
        return 'dogecoin';
    }

    public function displayName(): string
    {
        return 'Dogecoin';
    }

    public function style(): string
    {
        return '#c2a633';
    }

    public function nowpayments(): string
    {
        return 'doge';
    }
}
