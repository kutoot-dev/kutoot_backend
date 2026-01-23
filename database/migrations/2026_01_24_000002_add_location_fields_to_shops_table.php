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
        Schema::table('shops', function (Blueprint $table) {
            // Drop old string columns if they exist
            if (Schema::hasColumn('shops', 'state')) {
                $table->dropColumn('state');
            }
            if (Schema::hasColumn('shops', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('shops', 'country')) {
                $table->dropColumn('country');
            }
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('address');
            $table->unsignedBigInteger('state_id')->nullable()->after('country_id');
            $table->unsignedBigInteger('city_id')->nullable()->after('state_id');

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);
            $table->dropColumn(['country_id', 'state_id', 'city_id']);
        });

        Schema::table('shops', function (Blueprint $table) {
            $table->string('state')->nullable()->after('address');
            $table->string('city')->nullable()->after('state');
            $table->string('country')->nullable()->after('city');
        });
    }
};
