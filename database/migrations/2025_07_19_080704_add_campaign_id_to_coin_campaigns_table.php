<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('coin_campaigns', 'campaign_id')) {
            Schema::table('coin_campaigns', function (Blueprint $table) {
                $table->string('campaign_id')->after('id');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('coin_campaigns', 'campaign_id')) {
            Schema::table('coin_campaigns', function (Blueprint $table) {
                $table->dropColumn('campaign_id');
            });
        }
    }
};
