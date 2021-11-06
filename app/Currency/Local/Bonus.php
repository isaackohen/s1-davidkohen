<?php namespace App\Currency\Local;

use App\Currency\Option\WalletOption;

class Bonus extends LocalCurrency {

    function id(): string {
        return "local_bonus";
    }

    function walletId(): string {
        return "bonus";
    }

    function name(): string {
        return "BONUS";
    }

    function alias(): string {
        return "bonus";
    }

    function displayName(): string {
        return "BONUS";
    }

    public function tokenPrice() {
        return 1;
    }
	
	protected function options(): array {
        return [];
    }

}
