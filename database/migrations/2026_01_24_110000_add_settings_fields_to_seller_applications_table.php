<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->decimal('commission_percent', 5, 2)->nullable()->after('min_bill_amount');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('commission_percent');
            $table->decimal('rating', 3, 2)->nullable()->after('discount_percent');
            $table->string('store_image')->nullable()->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_applications', function (Blueprint $table) {
            $table->dropColumn(['commission_percent', 'discount_percent', 'rating', 'store_image']);
        });
    }
};
