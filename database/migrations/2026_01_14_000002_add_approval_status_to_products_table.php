<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new approval_status column if it doesn't exist
        if (!Schema::hasColumn('products', 'approval_status')) {
            Schema::table('products', function (Blueprint $table) {
                $table->tinyInteger('approval_status')->default(0)->after('status');
            });

            // Migrate existing data from approve_by_admin to approval_status
            // approve_by_admin: 1 (approved) -> approval_status: 1 (approved)
            // approve_by_admin: 0 (pending) -> approval_status: 0 (pending)
            if (Schema::hasColumn('products', 'approve_by_admin')) {
                DB::statement('UPDATE products SET approval_status = COALESCE(approve_by_admin, 1)');

                // Drop the old column after migration
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('approve_by_admin');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};
