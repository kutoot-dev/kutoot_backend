<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSponsoredByToCoinCampaignsTable extends Migration
{
    public function up()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->string('sponsored_by')->nullable()->after('highlights');
        });
    }

    public function down()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->dropColumn('sponsored_by');
        });
    }
}
