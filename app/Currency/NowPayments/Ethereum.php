<?php

namespace App\Currency\NowPayments;

class Ethereum extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_eth';
    }

    public function walletId(): string
    {
        return 'eth';
    }

    public function name(): string
    {
        return 'ETH';
    }

    public function alias(): string
    {
        return 'ethereum';
    }

    public function displayName(): string
    {
        return 'Ethereum';
    }

    public function style(): string
    {
        return '#627eea';
    }

    public function nowpayments(): string
    {
        return 'eth';
    }
}
