<?php namespace App\Currency;

use App\Currency\BitGo\BitGoCurrency;
use App\Currency\Option\WalletOption;
use App\Events\Deposit;
use App\Invoice;
use App\Settings;
use App\Transaction;
use App\Statistics;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\Decimal128;

abstract class Currency {

    abstract function id(): string;

    abstract function walletId(): string;

    abstract function name(): string;

    abstract function alias(): string;

    abstract function displayName(): string;

    abstract function style(): string;

    abstract function newWalletAddress(): string;

    protected abstract function options(): array;

    public function minBet(): float {
        return 0.0000001;
    }

    public function tokenPrice() {
        try {
            if (!Cache::has('conversions:' . $this->alias()))
                Cache::put('conversions:' . $this->alias(), file_get_contents("https://api.coingecko.com/api/v3/coins/{$this->alias()}?localization=false&market_data=true"), now()->addHours(1));
            $json = json_decode(Cache::get('conversions:' . $this->alias()));
            return $json->market_data->current_price->usd;
        } catch (\Exception $e) {
			try{
			   $result = Cache::remember('rate', 60, function () {
					return file_get_contents("https://min-api.cryptocompare.com/data/pricemulti?fsyms={$this->name()}&tsyms=USD");
			   });
			   $price = json_decode($result, true);
			   return $price[$this->name()]['USD'];
			} catch (\Exception $e) {
				return 1;
			}
        }
    }

    public function convertUSDToToken(float $usdAmount) {
        return $usdAmount / $this->tokenPrice();
    }

    public function convertTokenToUSD(float $tokenAmount) {
        return $tokenAmount * $this->tokenPrice();
    }

    public function getBotBet() {
        $getBotbet = $this->randomBotBet($this->convertUSDToToken(0.15), $this->convertUSDToToken(5));
        return $getBotbet;
    }


    /**
     * Gets random bet value. Higher values are less common.
     * @param $min
     * @param $max
     * @return mixed
     */
    protected function randomBotBet(float $min, float $max) {
        try {
            $diff = 100000000;
            return min(mt_rand($min * $diff, $max * $diff) / $diff, mt_rand($min * $diff, $max * $diff) / $diff);
        } catch (\Exception $e) {
            return $this->randomBotBet(1, 100);
        }
    }



    /** @return WalletOption[] */
    public function getOptions(): array {
        return array_merge($this->options(), [
			new class extends WalletOption {
                function id() {
                    return "icon";
                }

                function name(): string {
                    return "Icon crypto";
                }
            },
            new class extends WalletOption {
                function id() {
                    return "demo";
                }

                function name(): string {
                    return "Demo value";
                }
            },
            new class extends WalletOption {
                function id() {
                    return "fee";
                }

                function name(): string {
                    return "Transaction fee";
                }
            },
            new class extends WalletOption {
                function id() {
                    return "withdraw";
                }

                function name(): string {
                    return "Minimal withdraw amount";
                }
            },
			new class extends WalletOption {
                function id() {
                    return "withdraw_manual_trigger";
                }

                function name(): string {
                    return "Withdrawal manually if balance including total withdrawal amount is higher than";
                }
            },
			new class extends WalletOption {
                function id() {
                    return "min_tip";
                }

                function name(): string {
                    return "Minimum Tip Amount";
                }
            },
			new class extends WalletOption {
                function id() {
                    return "min_rain";
                }

                function name(): string {
                    return "Minimum Rain Amount";
                }
            },
			new class extends WalletOption {
                function id() {
                    return "min_bet";
                }

                function name(): string {
                    return "Min. bet";
                }
            },
			new class extends WalletOption {
                function id() {
                    return "max_bet";
                }

                function name(): string {
                    return "Max. bet";
                }
            },            
			new class extends WalletOption {
                public function id() {
                    return 'quiz';
                }

                public function name(): string {
                    return 'Quiz answer reward';
                }
            },
			new class extends WalletOption {
                function id() {
                    return 'high_roller_requirement';
                }

                function name(): string {
                    return '"High Rollers" tab min bet amount';
                }
            },
        ]);
    }

    public function option(string $key, string $value = null): string {
        if($value == null) {
            if(Cache::has('currency:'.$this->walletId().':'.$key)) return json_decode(Cache::get('currency:'.$this->walletId().':'.$key), true)[$key] ?? '1';
            return \App\Currency::where('currency', $this->walletId())->first()->data[$key] ?? '1';
        }

        $data = \App\Currency::where('currency', $this->walletId())->first();

        if(!$data) $data = \App\Currency::create(['currency' => $this->walletId(), 'data' => []]);

        $data = $data->data;
        $data[$key] = $value;

        \App\Currency::where('currency', $this->walletId())->first()->update([
            'data' => $data
        ]);

        Cache::forget('currency:'.$this->walletId().':'.$key);
        Cache::put('currency:'.$this->walletId().':'.$key, json_encode($data), now()->addYear());
        return $value;
    }

    abstract function isRunning(): bool;

    /**
     * @param string|null $wallet Null for every transaction except local nodes
     * @return mixed
     */
    abstract function process(string $wallet = null);

    abstract function send(string $from, string $to, float $sum);

    abstract function setupWallet();
	
	abstract function depositmethod();
	
	abstract function withdrawmethod();

    abstract function coldWalletBalance(): float;

    abstract function hotWalletBalance(): float;

    public static function toCurrencyArray(array $array) {
        $currency = [];
        foreach($array as $c) {
			if((!auth('sanctum')->user()->first_deposit_bonus) && ($c->id() === 'local_bonus')) continue;
			$currency = array_merge($currency, [
				$c->id() => [
					'id' => $c->id(),
					'walletId' => $c->walletId(),
					'name' => $c->name(),
					'displayName' => $c->displayName(),
					'icon' => $c->option('icon'),
					'style' => $c->style(),
					'price' => $c->tokenPrice(),
					'withdrawFee' => floatval($c->option('fee')),
					'minimalWithdraw' => floatval($c->option('withdraw')),
					'highRollerRequirement' => floatval($c->option('high_roller_requirement')),
					'min_bet' => floatval($c->option('min_bet')),
					'max_bet' => floatval($c->option('max_bet')),
					'balance' => [
						'real' => auth('sanctum')->guest() ? null : auth('sanctum')->user()->balance($c)->get(),
						'demo' => auth('sanctum')->guest() ? null : auth('sanctum')->user()->balance($c)->demo(true)->get()
					]
				],
				'vip' => [
						'breakpoints' => [
							1 => floatval(Settings::get('vip_ruby_usd')),
							2 => floatval(Settings::get('vip_emerald_usd')),
							3 => floatval(Settings::get('vip_sapphire_usd')),
							4 => floatval(Settings::get('vip_diamond_usd')),
							5 => floatval(Settings::get('vip_gold_usd'))
						]
				],
				'vipClosest' => Currency::find('np_eth')->name(),
				'vipClosestId' => Currency::find('np_eth')->id(),
				'vipClosestWager' => auth('sanctum')->guest() ? 0 : (Statistics::where('user', auth('sanctum')->user()->_id)->first()->data['usd_wager'] ?? 0)
			]);
		}

        return $currency;
    }

    public static function getAllSupportedCoins(): array {
        return [
            new Local\Rub(),
            new Local\Usd(),
			new Local\Bonus(),
			new Native\Bitcoin(),
            new Native\Ethereum(),
            new Native\Litecoin(),
            new Native\Dogecoin(),
            new Native\Litecoin(),
            new Native\BitcoinCash(),
			new NowPayments\Bitcoin(),
            new NowPayments\Ethereum(),
            new NowPayments\Litecoin(),
            new NowPayments\Dogecoin(),
            new NowPayments\Litecoin(),
            new NowPayments\BitcoinCash(),
			new NowPayments\Tron(),
			new СhainGateway\BNB(),
			new СhainGateway\BUSD(),
			new СhainGateway\Pirate(),
            new BitGo\Bitcoin(),
            new BitGo\BitcoinCash(),
            new BitGo\BitcoinGold(),
            new BitGo\WrappedBitcoin(),
            new BitGo\Algorand(),
            new BitGo\Celo(),
            new BitGo\Dash(),
            new BitGo\EOS(),
            new BitGo\Ethereum(),
            new BitGo\Litecoin(),
            new BitGo\Ripple(),
            new BitGo\Stellar(),
            new BitGo\Tezos(),
            new BitGo\Tron(),
            new BitGo\ZCash()
        ];
    }

    public static function all(): array {
        $currencies = json_decode(Settings::get('currencies', '["native_btc"]', true));
        $result = [];
        foreach($currencies as $currency) array_push($result, Currency::find($currency));
        return $result;
    }

    public static function getByWalletId($walletId): array {
        $result = [];
        foreach(self::getAllSupportedCoins() as $coin) if($coin->walletId() === $walletId) array_push($result, $coin);
        return $result;
    }

    public static function find(string $id): ?Currency {
        foreach (self::getAllSupportedCoins() as $currency) if($currency->id() == $id) {
            if(\App\Currency::where('currency', $currency->id())->first() == null) {
                \App\Currency::create([
                    'currency' => $currency->id(),
                    'data' => []
                ]);
            }
            return $currency;
        }
        return null;
    }

    protected function accept(int $confirmations, string $wallet, string $id, float $sum) {
        $user = User::where('wallet_'.$this->id(), $wallet)->first();
        if($user == null) return false;

        $invoice = Invoice::where('id', $id)->first();
        if($invoice == null) {
            $invoice = Invoice::create([
                'user' => $user->_id,
                'sum' => new Decimal128($sum),
                'type' => 'currency',
                'currency' => $this->id(),
                'id' => $id,
                'confirmations' => $confirmations,
                'status' => 0
            ]);
            event(new Deposit($user, $this, $sum));
        } else $invoice->update([
            'confirmations' => $confirmations
        ]);

        if($invoice->status == 0 && $invoice->confirmations >= intval($this->option('confirmations'))) {
            $invoice->update(['status' => 1]);
            $user->balance($this)->add($sum, Transaction::builder()->message('Deposit')->get());

            if(!($this instanceof BitGoCurrency)) $this->send($wallet, $this->option('transfer_address'), $sum);

            if($user->referral) {
                $referrer = User::where('_id', $user->referral)->first();

                $commissionPercent = 0;

                switch ($referrer->vipLevel()) {
                    case 0: $commissionPercent = 1; break;
                    case 1: $commissionPercent = 2; break;
                    case 2: $commissionPercent = 3; break;
                    case 3: $commissionPercent = 4; break;
                    case 4: $commissionPercent = 5; break;
                    case 5: $commissionPercent = 7; break;
                }

                if($commissionPercent !== 0) {
                    $commission = ($commissionPercent * $sum) / 100;
                    $referrer->balance($this)->add($commission, Transaction::builder()->message('Affiliate commission (' . $commissionPercent . '% from ' . $sum . ' .' . $this->name() . ')')->get());
                }
            }
        }

        return true;
    }

}
