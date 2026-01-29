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
                $table->string('campaign_id');
                $table->string('title1')->nullable();
                $table->string('title2')->nullable();
                $table->string('title')->index();
                $table->text('description')->nullable();
                $table->json('highlights')->nullable();
                $table->string('sponsored_by')->nullable();
                $table->text('details_content')->nullable();
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
                $table->char('series_prefix', 1);
                $table->unsignedTinyInteger('number_min')->default(1);
                $table->unsignedTinyInteger('number_max');
                $table->unsignedTinyInteger('numbers_per_ticket');
                $table->string('tag1')->nullable();
                $table->string('tag2')->nullable();
                $table->string('image1')->nullable();
                $table->string('image2')->nullable();
                $table->text('short_description')->nullable();
                $table->date('winner_announcement_date')->nullable();
                $table->integer('marketing_start_percent')->default(10);
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
