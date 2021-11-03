<?php namespace App\Currency\СhainGateway;

class BNB extends СhainGatewaySupport {

    function id(): string {
        return 'cg_bnb';
    }

    public function walletId(): string {
        return "bnb";
    }

    function name(): string {
        return 'BNB';
    }

    public function alias(): string {
        return 'bnb';
    }

    public function displayName(): string {
        return "BNB";
    }

    public function style(): string {
        return "#ebd10a";
    }

}
