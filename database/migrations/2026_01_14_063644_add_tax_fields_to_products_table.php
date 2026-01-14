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
        Schema::table('products', function (Blueprint $table) {
            $table->string('hsn')->nullable()->after('sku');
            $table->double('cgst')->default(0)->after('price');
            $table->double('sgst')->default(0)->after('cgst');
            $table->double('igst')->default(0)->after('sgst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['hsn', 'cgst', 'sgst', 'igst']);
        });
    }
};
