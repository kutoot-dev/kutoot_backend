<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSplitFieldsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('commission_amount', 12, 2)->default(0)->after('total_amount');
            $table->decimal('shop_amount', 12, 2)->default(0)->after('commission_amount');
            $table->string('razorpay_payment_id')->nullable()->after('shop_amount');
            $table->string('razorpay_order_id')->nullable()->after('razorpay_payment_id');
            $table->json('split_details')->nullable()->after('razorpay_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['commission_amount', 'shop_amount', 'razorpay_payment_id', 'razorpay_order_id', 'split_details']);
        });
    }
}
