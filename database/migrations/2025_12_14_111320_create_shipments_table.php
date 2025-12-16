<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');

            $table->string('shipment_id')->nullable();
            $table->string('awb_code')->nullable();
            $table->string('courier_name')->nullable();
            $table->string('shipping_status')->default('created');
            $table->text('tracking_url')->nullable();

            $table->json('response')->nullable(); // full shiprocket response
            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipments');
    }
};
