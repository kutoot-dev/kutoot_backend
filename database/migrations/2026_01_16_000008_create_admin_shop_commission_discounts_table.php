<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminShopCommissionDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('admin_shop_commission_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedInteger('commission_percent')->default(0);
            $table->unsignedInteger('discount_percent')->default(0);
            $table->decimal('minimum_bill_amount', 12, 2)->default(0);
            $table->decimal('rating', 2, 1)->default(0.0);
            $table->integer('total_ratings')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('offer_tag')->nullable();
            $table->date('last_updated_on')->nullable();
            $table->timestamps();

            $table->index('shop_id');
            $table->unique('shop_id');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_shop_commission_discounts');
    }
}


