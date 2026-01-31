<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class StoreMigrateFresh extends Command
{
    protected $signature = 'store:migrate:fresh {--seed : Seed the Store module after migration} {--force : Force the operation to run when in production}';

    protected $description = 'Drop & re-run only Store module migrations';

    public function handle()
    {
        // Hard drop store tables (fresh project baseline). This avoids conflicts if store
        // tables were previously created but tracked in a different migrations table.
        Schema::disableForeignKeyConstraints();
        foreach ([
            'transactions',
            'shop_visitors',
            'shop_images',
            'store_categories',
            'seller_applications',
            'seller_bank_accounts',
            'seller_notification_settings',
            'store_sellers',
            'store_migrations',
        ] as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }
        Schema::enableForeignKeyConstraints();

        // Recreate only Store module schema using the dedicated store migration repository.
        $repository = new DatabaseMigrationRepository(app('db'), 'store_migrations');
        if (! $repository->repositoryExists()) {
            $repository->createRepository();
        }

        $migrator = new Migrator($repository, app('db'), app('files'), app('events'));
        $migrator->setOutput($this->output);
        $migrator->run([database_path('migrations/store')], [
            'pretend' => false,
            'step' => false,
        ]);

        if ($this->option('seed')) {
            Artisan::call('store:seed', ['--force' => $this->option('force')], $this->getOutput());
        }

        return self::SUCCESS;
    }
}


