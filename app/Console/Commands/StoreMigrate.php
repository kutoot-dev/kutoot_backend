<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;

class StoreMigrate extends Command
{
    protected $signature = 'store:migrate {--force : Force the operation to run when in production}';

    protected $description = 'Run only Store module migrations (database/migrations/store)';

    public function handle()
    {
        // Use a dedicated migration repository table for Store module.
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

        return self::SUCCESS;
    }
}


