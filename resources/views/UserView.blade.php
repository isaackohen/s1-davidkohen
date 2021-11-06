        @php
            $user = \App\User::where('_id', $crud->entry->_id)->first();
            $getUserStats = \App\User::where('_id', '=', $crud->entry->_id)->get();
            //$getBonusHistory = \App\Models\BonusHistory::where('u', '=', $crud->entry->_id)->first();
            $encode = json_encode($user->makeVisible('register_multiaccount_hash')->makeVisible('login_multiaccount_hash')->toArray());

            $register_multiaccount_hash = \App\User::where('register_multiaccount_hash', $user->register_multiaccount_hash)->get()->count();
            $same_login_hash = \App\User::where('login_multiaccount_hash', $user->login_multiaccount_hash)->get()->count();
            $same_register_ip = \App\User::where('register_ip', $user->login_multiaccount_hash)->get()->count();
            $same_login_ip = \App\User::where('login_ip', $user->login_ip)->get()->count();

        @endphp
<script>
new Noty({
    type: "success",
    text: 'Parsed User information',
}).show();
</script>
 <div class="row">
    <div class="container-fluid">

        <div class="tab-content p-0 ">
        <p><b>Register Multi Account:</b> {{ $register_multiaccount_hash }}
        <b>Same Login Hash:</b> {{ $same_login_hash }}
        <b>Same Register IP:</b> {{ $same_register_ip }}
        <b>Same Login IP:</b> {{ $same_login_ip }}</p>
        @php
            $getUserStats = \App\Statistics::where('user', '=', $crud->entry->_id)->first();
            $getBonusHistory = \App\TransactionStatistics::where('user', '=', $crud->entry->_id)->first();
        @endphp


        @if($getUserStats)
      <h4><span class="text-capitalize">Player's Game Statistics</span></h4>
        <small>Download Player complete <a href="#">transactional log</a> in Excel format.</small>

        @php
            $data = $getUserStats->data ?? null;
            //$vipLevel = \App\User::where('id', $crud->entry->_id)->vipLevel();
        @endphp
        @if($data !== null)
        <table class="table">
          <thead>
            <tr>
              <th scope="col">Tot. Wager ($)</th>
              <th scope="col">Tot. Wins ($)</th>
              <th scope="col">Tot. Netto Win</th>
              <th scope="col">Games Played</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ round($data['usd_wager'], 2) ?? '0.00'}}$</th>
              <td>{{ round($data['usd_wins'], 2) ?? '0.00'}}$</th>
              <td>{{ round(($data['usd_wins'] - $data['usd_wager']), 2) ?? '0.00'}}$</th>
              <td>{{ round($data['games_played']) ?? '0'}}</td>
            </tr>
         
          </tbody>
        </table>
        <hr>
        @endif
        @if($getBonusHistory)
        <h4><span class="text-capitalize">Player's Bonus Statistics</span></h4>
            <table class="table">
          <thead>
            <tr>
              <th scope="col">Promocode ($)</th>
              <th scope="col">Weekly Bonuses ($)</th>
              <th scope="col">Partner Bonuses ($)</th>
              <th scope="col">Faucet Bonuses ($)</th>
              <th scope="col">Freespins Used (Tot)</th>
             </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $getBonusHistory->promocode.'$' ?? '-'}}</td>
              <td>{{ $getBonusHistory->weeklybonus.'$' ?? '-' }}</td>
              <td>{{ $getBonusHistory->partnerbonus.'$' ?? '-' }}</td>
              <td>{{ $getBonusHistory->faucet.'$' ?? '-' }}</td>
              <td>{{ $getBonusHistory->freespins_amount.' ' ?? '-' }}</td>
            </tr>
           </tbody>
        </table>
        @endif
        @if($getBonusHistory)
        <h4><span class="text-capitalize">Player's Deposits & Withdraws</span></h4>
            <table class="table">
          <thead>
            <tr>
              <th scope="col">Deposits Netto ($)</th>
              <th scope="col">Deposits Count</th>
              <th scope="col">Withdrawal Netto ($)</th>
              <th scope="col">Withdraws Count</th>
              <th scope="col">Deposit Bonus Profits ($)</th>
             </tr>
          </thead>
          <tbody>
            <tr>
              <td>{{ $getBonusHistory->deposit_total.'$' ?? '-'}}</td>
              <td>{{ $getBonusHistory->deposit_count.'x' ?? '-' }}</td>
              <td>{{ $getBonusHistory->withdraw_total.'$' ?? '-' }}</td>
              <td>{{ $getBonusHistory->withdraw_count.'x' ?? '-' }}</td>
              <td>{{ $getBonusHistory->depositbonus.'$' ?? '-' }}</td>
            </tr>
           </tbody>
        </table>
        @endif
        @else
        <i>No statistics yet.</i>
        @endif
        </div>
        </div>
        </div>
        </div>
