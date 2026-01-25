<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinbasePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('coinbase_plans')) {
        Schema::create('coinbase_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('camp_id')->nullable();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->float('ticket_price', 8, 2)->default(0.00);
            $table->string('img')->nullable();
            $table->integer('total_tickets')->default(0);
            $table->integer('coins_per_campaign')->default(0);
            $table->integer('coupons_per_campaign')->default(0);
            $table->string('point1')->nullable();
            $table->string('point2')->nullable();
            $table->string('point3')->nullable();
            $table->string('point4')->nullable();
            $table->string('point5')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coinbase_plans');
    }
}
