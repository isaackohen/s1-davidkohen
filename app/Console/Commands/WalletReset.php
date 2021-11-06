<?php

namespace App\Console\Commands;

use App\Currency\Currency;
use App\User;
use Illuminate\Console\Command;

class WalletReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:walletreset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset assigned wallet addresses';

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
    public function handle()
    {
        foreach (Currency::all() as $currency) {
            $wallet = 'wallet_'.$currency->id();
            User::query()->update([
                $wallet => null,
            ]);
        }
    }
}
