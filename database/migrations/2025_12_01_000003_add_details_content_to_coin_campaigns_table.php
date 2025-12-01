<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailsContentToCoinCampaignsTable extends Migration
{
    public function up()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->text('details_content')->nullable()->after('sponsored_by');
        });
    }

    public function down()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->dropColumn('details_content');
        });
    }
}
