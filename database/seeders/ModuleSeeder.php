<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        DB::table('modules')->insert([
            ['name' => 'website'],
            ['name' => 'E-commerce'],
            ['name' => 'Stores'],
            ['name' => 'Admin'],
            ['name' => 'Marketing'],
        ]);
    }
}
