<?php

namespace App\Console\Commands;

use App\Models\CoinLedger;
use App\Models\UserCoins;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateLegacyCoinsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:migrate-legacy
                            {--dry-run : Preview migration without inserting data}
                            {--backup : Create backup table before migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate legacy table_usercoins data to unified coin_ledger table';

    /**
     * Mapping of legacy types to new entry_type and coin_category.
     */
    protected $typeMapping = [
        // Credit types (coins_in > 0)
        'credit' => ['entry_type' => CoinLedger::TYPE_PAID_CREDIT, 'category' => CoinLedger::CAT_PAID],
        'purchase' => ['entry_type' => CoinLedger::TYPE_PAID_CREDIT, 'category' => CoinLedger::CAT_PAID],
        'bought' => ['entry_type' => CoinLedger::TYPE_PAID_CREDIT, 'category' => CoinLedger::CAT_PAID],
        'paid' => ['entry_type' => CoinLedger::TYPE_PAID_CREDIT, 'category' => CoinLedger::CAT_PAID],
        'reward' => ['entry_type' => CoinLedger::TYPE_REWARD_CREDIT, 'category' => CoinLedger::CAT_REWARD],
        'bonus' => ['entry_type' => CoinLedger::TYPE_REWARD_CREDIT, 'category' => CoinLedger::CAT_REWARD],
        'referral' => ['entry_type' => CoinLedger::TYPE_REWARD_CREDIT, 'category' => CoinLedger::CAT_REWARD],
        'promotional' => ['entry_type' => CoinLedger::TYPE_REWARD_CREDIT, 'category' => CoinLedger::CAT_REWARD],

        // Debit types (coins_out > 0)
        'debit' => ['entry_type' => CoinLedger::TYPE_REDEEM, 'category' => CoinLedger::CAT_PAID],
        'redeem' => ['entry_type' => CoinLedger::TYPE_REDEEM, 'category' => CoinLedger::CAT_PAID],
        'used' => ['entry_type' => CoinLedger::TYPE_REDEEM, 'category' => CoinLedger::CAT_PAID],
        'spent' => ['entry_type' => CoinLedger::TYPE_REDEEM, 'category' => CoinLedger::CAT_PAID],
        'expired' => ['entry_type' => CoinLedger::TYPE_EXPIRE, 'category' => CoinLedger::CAT_PAID],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $backup = $this->option('backup');

        $this->info('==============================================');
        $this->info('  LEGACY COIN MIGRATION TO UNIFIED LEDGER');
        $this->info('==============================================');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  DRY RUN MODE - No data will be inserted');
            $this->newLine();
        }

        // Check if legacy table exists
        if (!Schema::hasTable('table_usercoins')) {
            $this->error('Legacy table "table_usercoins" does not exist.');
            return 1;
        }

        // Count legacy records
        $totalLegacy = UserCoins::count();
        $this->info("Found {$totalLegacy} records in legacy table.");

        if ($totalLegacy === 0) {
            $this->info('No records to migrate.');
            return 0;
        }

        // Check for already migrated records
        $alreadyMigrated = CoinLedger::whereNotNull('metadata')
            ->whereRaw("JSON_EXTRACT(metadata, '$.legacy_id') IS NOT NULL")
            ->count();

        if ($alreadyMigrated > 0) {
            $this->warn("Found {$alreadyMigrated} already migrated records in coin_ledger.");
            if (!$this->confirm('Do you want to skip already migrated records and continue?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        // Create backup if requested
        if ($backup && !$dryRun) {
            $this->createBackup();
        }

        // Preview type distribution
        $this->showTypeDistribution();

        if (!$dryRun && !$this->confirm('Proceed with migration?')) {
            $this->info('Migration cancelled.');
            return 0;
        }

        // Perform migration
        $this->migrateRecords($dryRun);

        $this->newLine();
        $this->info('✅ Migration completed successfully!');

        return 0;
    }

    /**
     * Create backup of legacy table.
     */
    protected function createBackup(): void
    {
        $backupTable = 'table_usercoins_backup_' . date('Ymd_His');

        $this->info("Creating backup table: {$backupTable}");

        DB::statement("CREATE TABLE {$backupTable} AS SELECT * FROM table_usercoins");

        $this->info("✅ Backup created successfully.");
        $this->newLine();
    }

    /**
     * Show distribution of types in legacy table.
     */
    protected function showTypeDistribution(): void
    {
        $this->info('Type distribution in legacy table:');
        $this->newLine();

        $types = UserCoins::selectRaw('LOWER(type) as type, COUNT(*) as count, SUM(coins) as total_coins')
            ->groupBy(DB::raw('LOWER(type)'))
            ->get();

        $headers = ['Type', 'Count', 'Total Coins', 'Maps To'];
        $rows = [];

        foreach ($types as $type) {
            $mapping = $this->getTypeMapping($type->type);
            $rows[] = [
                $type->type,
                $type->count,
                $type->total_coins,
                $mapping['entry_type'] . ' / ' . $mapping['category'],
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();
    }

    /**
     * Get type mapping for legacy type.
     */
    protected function getTypeMapping(string $legacyType): array
    {
        $normalizedType = strtolower(trim($legacyType));

        if (isset($this->typeMapping[$normalizedType])) {
            return $this->typeMapping[$normalizedType];
        }

        // Default mapping based on common patterns
        if (str_contains($normalizedType, 'credit') || str_contains($normalizedType, 'add') || str_contains($normalizedType, 'buy')) {
            return ['entry_type' => CoinLedger::TYPE_PAID_CREDIT, 'category' => CoinLedger::CAT_PAID];
        }

        if (str_contains($normalizedType, 'reward') || str_contains($normalizedType, 'bonus') || str_contains($normalizedType, 'free')) {
            return ['entry_type' => CoinLedger::TYPE_REWARD_CREDIT, 'category' => CoinLedger::CAT_REWARD];
        }

        if (str_contains($normalizedType, 'debit') || str_contains($normalizedType, 'use') || str_contains($normalizedType, 'redeem')) {
            return ['entry_type' => CoinLedger::TYPE_REDEEM, 'category' => CoinLedger::CAT_PAID];
        }

        if (str_contains($normalizedType, 'expir')) {
            return ['entry_type' => CoinLedger::TYPE_EXPIRE, 'category' => CoinLedger::CAT_PAID];
        }

        // Ultimate fallback - assume paid credit
        return ['entry_type' => CoinLedger::TYPE_PAID_CREDIT, 'category' => CoinLedger::CAT_PAID];
    }

    /**
     * Migrate records from legacy table to unified ledger.
     */
    protected function migrateRecords(bool $dryRun): void
    {
        $this->info($dryRun ? 'Previewing migration...' : 'Migrating records...');

        $bar = $this->output->createProgressBar(UserCoins::count());
        $bar->start();

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        // Get already migrated legacy IDs
        $migratedIds = CoinLedger::whereNotNull('metadata')
            ->get()
            ->pluck('metadata.legacy_id')
            ->filter()
            ->toArray();

        UserCoins::chunk(500, function ($records) use ($dryRun, &$migrated, &$skipped, &$errors, $migratedIds, $bar) {
            $inserts = [];

            foreach ($records as $legacy) {
                // Skip if already migrated
                if (in_array($legacy->id, $migratedIds)) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                try {
                    $mapping = $this->getTypeMapping($legacy->type ?? 'credit');
                    $isDebit = in_array($mapping['entry_type'], [CoinLedger::TYPE_REDEEM, CoinLedger::TYPE_EXPIRE]);

                    // Build reference ID
                    $refId = null;
                    if ($legacy->purchased_camp_id) {
                        $refId = 'CAMP' . $legacy->purchased_camp_id;
                    } elseif ($legacy->order_id) {
                        $refId = 'ORD' . $legacy->order_id;
                    }

                    $inserts[] = [
                        'user_id' => $legacy->user_id,
                        'entry_type' => $mapping['entry_type'],
                        'coins_in' => $isDebit ? 0 : abs($legacy->coins),
                        'coins_out' => $isDebit ? abs($legacy->coins) : 0,
                        'coin_category' => $mapping['category'],
                        'expiry_date' => $legacy->coupon_expires ?? $legacy->coin_expires,
                        'reference_id' => $refId,
                        'metadata' => json_encode([
                            'legacy_id' => $legacy->id,
                            'legacy_type' => $legacy->type,
                            'migrated_at' => now()->toIso8601String(),
                        ]),
                        'created_at' => $legacy->created_at,
                        'updated_at' => $legacy->updated_at,
                    ];

                    $migrated++;
                } catch (\Exception $e) {
                    $errors++;
                    $this->error("Error processing record {$legacy->id}: {$e->getMessage()}");
                }

                $bar->advance();
            }

            if (!$dryRun && count($inserts) > 0) {
                DB::table('coin_ledger')->insert($inserts);
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info("Summary:");
        $this->info("  - Migrated: {$migrated}");
        $this->info("  - Skipped (already migrated): {$skipped}");
        $this->info("  - Errors: {$errors}");
    }
}
