<?php

namespace App\Console\Commands;

use App\Models\CoinLedger;
use App\Services\CoinLedgerService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireCoinsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily job to expire unused coins';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CoinLedgerService $service)
    {
        $this->info('Starting coin expiration check...');

        // Find credits that have expired but haven't been processed for expiration yet.
        // Processed means there is a TYPE_EXPIRE entry for it.
        $expiredCredits = CoinLedger::where('coins_in', '>', 0)
            ->where('expiry_date', '<', now())
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('coin_ledger as debits')
                    ->whereRaw('JSON_EXTRACT(debits.metadata, "$.source_id") = coin_ledger.id')
                    ->where('debits.entry_type', CoinLedger::TYPE_EXPIRE);
            })
            ->get();

        $count = 0;
        foreach ($expiredCredits as $credit) {
            if ($service->expireEntry($credit)) {
                $count++;
            }
        }

        $this->info("Successfully expired {$count} entries.");
        return 0;
    }
}
