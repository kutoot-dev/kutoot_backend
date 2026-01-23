<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove duplicate commission/discount/rating fields from seller_applications.
     * These are managed in admin_shop_commission_discounts table (single source of truth).
     */
    public function up(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->dropColumn(['commission_percent', 'discount_percent', 'rating', 'no_of_ratings', 'images']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->decimal('commission_percent', 5, 2)->nullable()->after('min_bill_amount');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('commission_percent');
            $table->decimal('rating', 3, 2)->nullable()->after('discount_percent');
            $table->integer('no_of_ratings')->default(0)->after('rating');
            $table->json('images')->nullable()->after('no_of_ratings');
        });
    }
};
