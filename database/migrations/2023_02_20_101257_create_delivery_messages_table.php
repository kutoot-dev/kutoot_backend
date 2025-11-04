<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('delivery_man_id');
            $table->integer('customer_id');
            $table->integer('order_id');
            $table->text('message');
            $table->string('sent_by');
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
        Schema::dropIfExists('delivery_messages');
    }
}
