<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionStatistics extends Model {

    protected $collection = 'transaction_statistics';
    protected $connection = 'mongodb';

    protected $fillable = [
        'user', 'promocode', 'weeklybonus', 'partnerbonus', 'freespins_amount', 'faucet', 'depositbonus', 'deposit_total', 'deposit_count', 'withdraw_count', 'withdraw_total', 'vip_progress'
    ];

    protected $casts = [
        'data' => 'json'
    ];



    public static function statsUpdate($userid, $type, $amount) {
        $stats = \App\TransactionStatistics::where('user', $userid)->first();

         if(!$stats) {
           \App\TransactionStatistics::create([
                'user' => $userid,
                'promocode' => 0,
                'weeklybonus' => 0,
                'freespins_amount' => 0,
                'partnerbonus' => 0,
                'faucet' => 0,
                'depositbonus' => 0,
                'deposit_total' => 0,
                'deposit_count' => 0,
                'withdraw_count' => 0,
                'withdraw_total' => 0,
                'vip_progress' => 0,
            ]);
        $stats = \App\TransactionStatistics::where('user', $userid)->first();
         }

        $selectCurrent = $stats->$type;
        $stats->update([
            $type => round($selectCurrent ?? 0, 3) + round($amount, 3),
        ]);

        if($type === 'deposit_total') {
            $stats->update([
                'deposit_count' => $stats->deposit_count + 1,
            ]);
        }
    }

}
