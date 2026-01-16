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
            $table->unsignedInteger('commission_percent')->default(0);
            $table->unsignedInteger('discount_percent')->default(0);
            $table->decimal('minimum_bill_amount', 12, 2)->default(0);
            $table->date('last_updated_on')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_shop_commission_discounts');
    }
}


