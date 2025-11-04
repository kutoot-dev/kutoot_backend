<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinCampaigns extends Migration
{
    protected $tableName = 'coin_campaigns';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable($this->tableName)) {
            Schema::create($this->tableName, function (Blueprint $table) {
                $table->id();
                $table->string('title')->index();
                $table->text('description')->nullable();
                $table->float('ticket_price', 8, 2)->default(0.00);
                $table->string('img')->nullable();
                $table->integer('total_tickets')->default(0);
                $table->integer('sold_tickets')->default(0);
                $table->integer('coins_per_campaign')->default(0);
                $table->integer('coupons_per_campaign')->default(0);
                $table->float('max_coins_per_transaction', 10, 2)->default(10.00)->comment('Maximum coins in percentage that can be used per transaction');
                $table->json('tags')->default(null)->comment('JSON array of tags for the campaign');
                $table->dateTime('start_date')->default(now());
                $table->dateTime('end_date')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->text('category')->nullable();
                $table->text('video')->nullable();
                $table->string('promotion')->nullable();
                $table->timestamps();
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
        if (Schema::hasTable($this->tableName)) {
            Schema::dropIfExists($this->tableName);
        }
    }
}
