<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_campaigns', function (Blueprint $table) {
              $table->id();
            $table->string('name')->unique(); // Campaign name
            $table->char('series_prefix', 1); // A–Z prefix
            $table->unsignedTinyInteger('number_min')->default(1); // e.g. 1
            $table->unsignedTinyInteger('number_max');             // e.g. 49
            $table->unsignedTinyInteger('numbers_per_ticket');     // e.g. 3, 4, 5, 6
            $table->unsignedBigInteger('max_combinations')->nullable(); // optional pre-calculated limit
            $table->unsignedBigInteger('tickets_issued')->default(0);   // auto-increment per ticket issued
            $table->unsignedBigInteger('goal_target')->nullable();      // optional: to simulate progress

            $table->boolean('is_active')->default(false);
            $table->boolean('draw_triggered')->default(false);
            
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
        Schema::dropIfExists('coupon_campaigns');
    }
}
