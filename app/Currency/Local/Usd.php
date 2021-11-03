<?php namespace App\Currency\Local;

use App\Currency\Option\WalletOption;

class Usd extends LocalCurrency {

    function id(): string {
        return "local_usd";
    }

    function walletId(): string {
        return "usd";
    }

    function name(): string {
        return "USD";
    }

    function alias(): string {
        return "usd";
    }

    function displayName(): string {
        return "USD";
    }

    protected function options(): array {
        return [
            new class extends WalletOption {
                function id() {
                    return "bulkcash_usd";
                }
                function name(): string {
                    return "USD";
                }
            }
        ];
    }

    public function tokenPrice() {
        return 1;
    }

}
