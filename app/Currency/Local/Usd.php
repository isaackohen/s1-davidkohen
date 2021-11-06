<?php

namespace App\Currency\Local;

use App\Currency\Option\WalletOption;

class Usd extends LocalCurrency
{
    public function id(): string
    {
        return 'local_usd';
    }

    public function walletId(): string
    {
        return 'usd';
    }

    public function name(): string
    {
        return 'USD';
    }

    public function alias(): string
    {
        return 'usd';
    }

    public function displayName(): string
    {
        return 'USD';
    }

    protected function options(): array
    {
        return [
            new class extends WalletOption {
                public function id()
                {
                    return 'bulkcash_usd';
                }

                public function name(): string
                {
                    return 'USD';
                }
            },
        ];
    }

    public function tokenPrice()
    {
        return 1;
    }
}
