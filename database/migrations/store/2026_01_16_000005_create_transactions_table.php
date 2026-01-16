<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('visitor_id')->nullable();
            $table->string('txn_code')->unique();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->enum('status', ['SUCCESS', 'FAILED'])->default('SUCCESS');
            $table->date('settled_at')->nullable();
            $table->timestamps();

            $table
                ->foreign('shop_id')
                ->references('id')
                ->on('shops')
                ->onDelete('cascade');

            $table
                ->foreign('visitor_id')
                ->references('id')
                ->on('shop_visitors')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}


