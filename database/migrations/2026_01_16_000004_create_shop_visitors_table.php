<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopVisitorsTable extends Migration
{
    public function up()
    {
        Schema::create('shop_visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->date('visited_on')->nullable();
            $table->boolean('redeemed')->default(false);
            $table->timestamps();

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shop_visitors');
    }
}


