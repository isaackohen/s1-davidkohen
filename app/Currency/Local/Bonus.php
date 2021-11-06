<?php

namespace App\Currency\Local;

use App\Currency\Option\WalletOption;

class Bonus extends LocalCurrency
{
    public function id(): string
    {
        return 'local_bonus';
    }

    public function walletId(): string
    {
        return 'bonus';
    }

    public function name(): string
    {
        return 'BONUS';
    }

    public function alias(): string
    {
        return 'bonus';
    }

    public function displayName(): string
    {
        return 'BONUS';
    }

    public function tokenPrice()
    {
        return 1;
    }

    protected function options(): array
    {
        return [];
    }
}
