<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusFieldsToCoinCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coin_campaigns', function (Blueprint $table) {
            $table->decimal('marketing_goal_status', 5, 2)->default(0.00)->after('marketing_start_percent')->comment('Current percentage of tickets sold (0-100)');
            $table->decimal('actual_status', 5, 2)->default(0.00)->after('status')->comment('Overall campaign completion percentage (0-100)');
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
            $table->dropColumn(['marketing_goal_status', 'actual_status']);
        });
    }
}
