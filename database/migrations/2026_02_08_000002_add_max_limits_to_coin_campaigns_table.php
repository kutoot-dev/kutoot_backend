<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxLimitsToCoinCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->integer('max_coins')->nullable()->after('coins_per_campaign');
            $table->integer('max_coupons')->nullable()->after('coupons_per_campaign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->dropColumn(['max_coins', 'max_coupons']);
        });
    }
}
