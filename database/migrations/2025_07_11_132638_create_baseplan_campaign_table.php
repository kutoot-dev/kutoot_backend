<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseplanCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('baseplan_campaign_linked', function (Blueprint $table) {
            $table->id();
            $table->foreignId('baseplan_id')->constrained('coinbase_plans')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained('coin_campaigns')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('baseplan_campaign');
    }
}
