<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitlesToCoinCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('coin_campaigns', function (Blueprint $table) {
        $table->string('title1')->nullable()->after('id'); // or after any existing column
        $table->string('title2')->nullable()->after('title1');
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
            $table->dropColumn(['title1', 'title2']);
        });
    }
}
