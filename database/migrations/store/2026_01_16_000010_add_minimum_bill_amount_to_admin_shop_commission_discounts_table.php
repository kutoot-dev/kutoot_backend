<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMinimumBillAmountToAdminShopCommissionDiscountsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('admin_shop_commission_discounts', 'minimum_bill_amount')) {
            Schema::table('admin_shop_commission_discounts', function (Blueprint $table) {
                $table->decimal('minimum_bill_amount', 12, 2)->default(0)->after('discount_percent');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('admin_shop_commission_discounts', 'minimum_bill_amount')) {
            Schema::table('admin_shop_commission_discounts', function (Blueprint $table) {
                $table->dropColumn('minimum_bill_amount');
            });
        }
    }
}


