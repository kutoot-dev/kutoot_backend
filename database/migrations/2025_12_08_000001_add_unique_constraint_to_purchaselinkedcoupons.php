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
        // First, drop the existing unique index on coupon_code
        Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) {
            $table->dropIndex(['coupon_code']);
        });
        
        // Add unique constraint on combination of main_campaign_id and coupon_code
        Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) {
            $table->unique(['main_campaign_id', 'coupon_code'], 'unique_campaign_coupon');
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
            $table->dropUnique('unique_campaign_coupon');
            
            // Add back the single column index
            $table->index('coupon_code');
        });
    }
}
