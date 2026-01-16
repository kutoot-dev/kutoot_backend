<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleMapUrlToShopsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('shops')) {
            return;
        }

        if (Schema::hasColumn('shops', 'google_map_url')) {
            return;
        }

        Schema::table('shops', function (Blueprint $table) {
            $table->string('google_map_url', 2048)->nullable()->after('address');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('shops')) {
            return;
        }

        if (!Schema::hasColumn('shops', 'google_map_url')) {
            return;
        }

        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('google_map_url');
        });
    }
}


