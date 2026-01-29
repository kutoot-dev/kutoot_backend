<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coin_ledger', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('entry_type'); // PAID_COIN_CREDIT, REWARD_COIN_CREDIT, COIN_REDEEM, COIN_EXPIRE, COIN_REVERSAL
            $table->integer('coins_in')->default(0);
            $table->integer('coins_out')->default(0);
            $table->string('coin_category'); // PAID or REWARD
            $table->dateTime('expiry_date')->nullable();
            $table->string('reference_id')->nullable()->index(); // e.g., order_id, txn_id
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coin_ledger');
    }
}
