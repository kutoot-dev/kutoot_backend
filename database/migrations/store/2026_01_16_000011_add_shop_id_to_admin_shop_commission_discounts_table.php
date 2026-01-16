<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopIdToAdminShopCommissionDiscountsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('admin_shop_commission_discounts', 'shop_id')) {
            Schema::table('admin_shop_commission_discounts', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');

                $table->index('shop_id');
                $table->unique('shop_id');

                $table
                    ->foreign('shop_id')
                    ->references('id')
                    ->on('shops')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('admin_shop_commission_discounts', 'shop_id')) {
            Schema::table('admin_shop_commission_discounts', function (Blueprint $table) {
                // Drop FK + indexes safely (names are generated; use column-based helpers where possible)
                try {
                    $table->dropForeign(['shop_id']);
                } catch (\Throwable $e) {
                    // ignore
                }

                try {
                    $table->dropUnique(['shop_id']);
                } catch (\Throwable $e) {
                    // ignore
                }

                try {
                    $table->dropIndex(['shop_id']);
                } catch (\Throwable $e) {
                    // ignore
                }

                $table->dropColumn('shop_id');
            });
        }
    }
}


