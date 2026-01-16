<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class StoreSeed extends Command
{
    protected $signature = 'store:seed {--class=Database\\Seeders\\Store\\StoreDatabaseSeeder : The seeder class name} {--force : Force the operation to run when in production}';

    protected $description = 'Run only Store module seeders';

    public function handle()
    {
        $params = [
            '--class' => $this->option('class'),
        ];

        if ($this->option('force')) {
            $params['--force'] = true;
        }

        return Artisan::call('db:seed', $params, $this->getOutput());
    }
}


