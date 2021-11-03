<?php

namespace App\Console\Commands;

use App\User;
use App\Currency\Option\WalletOption;
use App\Events\Deposit;
use App\Invoice;
use App\Transaction;
use App\Settings;

use App\Currency\Currency;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MindepositUpdate extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:mindepositupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set min deposit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
	 
    public function handle() {

		foreach(Currency::all() as $currency) {
			if($currency->nowpayments()) {
				$apikey = env('NOWPAYMENTS_ID');
				try {
					$curlcurrency = curl_init();
					curl_setopt_array($curlcurrency, array(
					  CURLOPT_URL => 'https://api.nowpayments.io/v1/min-amount?currency_from='.$currency->nowpayments().'',
					  CURLOPT_RETURNTRANSFER => true,
					  CURLOPT_ENCODING => '',
					  CURLOPT_MAXREDIRS => 10,
					  CURLOPT_TIMEOUT => 0,
					  CURLOPT_FOLLOWLOCATION => true,
					  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					  CURLOPT_CUSTOMREQUEST => 'GET',
					  CURLOPT_HTTPHEADER => array(
						   "x-api-key: ".$apikey."",
					  ),
					));
					$responsecurl = curl_exec($curlcurrency);
					curl_close($curlcurrency);
					$responseCurrency = json_decode($responsecurl);
					Log::info($responsecurl);
					$mindeposit = $responseCurrency->min_amount;
					$mindepositusd = $mindeposit * $currency->tokenPrice();
					$val = 'nowpayments_min_'.$currency->nowpayments();
					if(Settings::where('name', $val)->first() === null){
						Settings::create(['name' => $val, 'description' => 'NowPayments min deposit '.$currency->nowpayments(), 'value' => 0]);
					} 
					Settings::where('name', $val)->update(['value' => $mindepositusd]);
				} catch (\Exception $exception) {
					$this->error($exception);
				}
			}
		}
	}
	
}