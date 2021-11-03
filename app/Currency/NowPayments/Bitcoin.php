<?php namespace App\Currency\NowPayments;

class Bitcoin extends NowPaymentsSupport {

    function id(): string {
        return "np_btc";
    }

    public function walletId(): string {
        return "btc";
    }

    function name(): string {
        return "BTC";
    }

    public function alias(): string {
        return "bitcoin";
    }

    public function displayName(): string {
        return "Bitcoin";
    }

    public function style(): string {
        return "#f7931a";
    }
	
	public function nowpayments(): string {
        return 'btc';
    }

}
