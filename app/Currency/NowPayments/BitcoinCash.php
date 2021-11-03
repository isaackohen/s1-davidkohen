<?php namespace App\Currency\NowPayments;

class BitcoinCash extends NowPaymentsSupport {

    function id(): string {
        return "np_bch";
    }

    public function walletId(): string {
        return "bch";
    }

    function name(): string {
        return "BCH";
    }

    public function alias(): string {
        return 'bitcoin-cash';
    }

    public function displayName(): string {
        return "Bitcoin Cash";
    }

    public function style(): string {
        return "#8dc351";
    }
	
	public function nowpayments(): string {
        return 'bch';
    }

}
