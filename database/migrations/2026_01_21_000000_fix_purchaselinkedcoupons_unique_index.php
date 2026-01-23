<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Get existing indexes on the table
        $indexes = collect(DB::select("SHOW INDEX FROM table_purchaselinkedcoupons"))
            ->pluck('Key_name')
            ->unique()
            ->toArray();

        // Check for various possible index names and drop if exists
        $possibleNames = [
            'coupon_code',
            'table_purchaselinkedcoupons_coupon_code_unique',
            'table_purchaselinkedcoupons_coupon_code_index',
        ];

        foreach ($possibleNames as $indexName) {
            if (in_array($indexName, $indexes)) {
                Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
                break;
            }
        }

        // Check if new index already exists before creating
        if (!in_array('unique_campaign_series_coupon', $indexes)) {
            Schema::table('table_purchaselinkedcoupons', function (Blueprint $table) {
                // Add the new unique constraint including series_label
                $table->unique(['main_campaign_id', 'series_label', 'coupon_code'], 'unique_campaign_series_coupon');
            });
        }
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
