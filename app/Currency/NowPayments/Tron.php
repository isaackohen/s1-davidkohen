<?php

namespace App\Currency\NowPayments;

class Tron extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_trx';
    }

    public function walletId(): string
    {
        return 'trx';
    }

    public function name(): string
    {
        return 'TRX';
    }

    public function alias(): string
    {
        return 'tron';
    }

    public function displayName(): string
    {
        return 'Tron (TRX)';
    }

    public function style(): string
    {
        return '#eb0a29';
    }

    public function nowpayments(): string
    {
        return 'trx';
    }
}
