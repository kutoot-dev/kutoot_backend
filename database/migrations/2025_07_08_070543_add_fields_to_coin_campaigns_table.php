<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToCoinCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->char('series_prefix', 1); // A–Z prefix
            $table->unsignedTinyInteger('number_min')->default(1); // e.g. 1
            $table->unsignedTinyInteger('number_max');             // e.g. 49
            $table->unsignedTinyInteger('numbers_per_ticket');     // e.g. 3, 4, 5, 6
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
            $table->dropColumn(['series_prefix', 'number_min', 'number_max', 'numbers_per_ticket']);
        });
    }
}
