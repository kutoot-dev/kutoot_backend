<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagsToCoinCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
             $table->string('tag1')->nullable();
            $table->string('tag2')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->text('short_description')->nullable();
            $table->date('winner_announcement_date')->nullable();
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
            $table->dropColumn(['tag1', 'tag2', 'image1', 'image2', 'short_description', 'winner_announcement_date']);
        });
    }
}
