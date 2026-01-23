<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixPurchaselinkedcouponsUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) {
            // Try to drop the unique index on coupon_code (may have different naming conventions)
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('table_purchaselinkedcoupons');

            // Check for various possible index names
            $possibleNames = [
                'coupon_code',
                'table_purchaselinkedcoupons_coupon_code_unique',
                'table_purchaselinkedcoupons_coupon_code_index',
            ];

            foreach ($possibleNames as $indexName) {
                if (isset($indexes[$indexName])) {
                    $table->dropIndex($indexName);
                    break;
                }
            }
        });

        Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) {
            // Add the new unique constraint including series_label
            $table->unique(['main_campaign_id', 'series_label', 'coupon_code'], 'unique_campaign_series_coupon');
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
            // Drop the new unique constraint
            $table->dropUnique('unique_campaign_series_coupon');

            // Restore the old global unique index
            $table->index('coupon_code'); // Was it just an index or unique?
            // The original migration "2025_06_06_095902" said ->index().
            // But the error said "Duplicate entry ... for key ...".
            // If it was just an index, duplicates are allowed.
            // If it was unique, then Restore ->unique('coupon_code').
            // The dump showed Non_unique: 0, so it WAS unique.
            // Wait, if 2025_06_06 created ->index(), it should be Non_unique: 1.
            // Unless 2025_12_08 changed it? But 2025_12_08 failed?
            // Let's assume we want to restore strictly what leads to the error if we rollback,
            // causing the error again is probably correct for "down".
            // But actually, we probably want to restore the previous state.
            // If the previous state was invalid, we might just leave it.
            // Let's restore a normal index or unique index based on observation.
            // Observation: coupon_code is UNIQUE (Non_unique: 0).
            $table->unique('coupon_code');
        });
    }
}
