<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToPurchaselinkedcoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) {
            // Drop existing index on coupon_code if exists
            $table->dropIndex(['coupon_code']);
            
            // Add unique constraint on combination of purchased_camp_id and coupon_code
            $table->unique(['purchased_camp_id', 'coupon_code'], 'unique_purchased_camp_coupon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('unique_purchased_camp_coupon');
            
            // Add back the single column index
            $table->index('coupon_code');
        });
    }
}
