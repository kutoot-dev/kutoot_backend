<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsInTablePurchasecoins extends Migration
{
    public function up()
    {
        Schema::table('table_purchasecoins', function (Blueprint $table) {
            // Rename column if it exists
            // if (Schema::hasColumn('table_purchasecoins', 'razor_order_id')) {
            //     $table->renameColumn('razor_order_id', 'razorpay_order_id');
            // }

            // Modify payment_id to string (if column exists)
            if (Schema::hasColumn('table_purchasecoins', 'payment_id')) {
                // $table->string('payment_id')->change();
            }

            // Add new column if it doesn’t exist
            if (!Schema::hasColumn('table_purchasecoins', 'razorpay_signature')) {
                // $table->string('razorpay_signature')->nullable()->after('payment_status');
            }
        });
    }

    public function down()
    {
        Schema::table('table_purchasecoins', function (Blueprint $table) {
            if (Schema::hasColumn('table_purchasecoins', 'razorpay_order_id')) {
                // $table->renameColumn('razorpay_order_id', 'razor_order_id');
            }

            if (Schema::hasColumn('table_purchasecoins', 'payment_id')) {
                // $table->integer('payment_id')->change();
            }

            if (Schema::hasColumn('table_purchasecoins', 'razorpay_signature')) {
                // $table->dropColumn('razorpay_signature');
            }
        });
    }
}
