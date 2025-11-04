<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_tickets', function (Blueprint $table) {
           $table->id();

            // Reference to coupon campaign
            $table->foreignId('campaign_id')
                ->constrained('coupon_campaigns')
                ->onDelete('cascade');

            $table->string('ticket_code')->unique();        // e.g., A-03-12-25
            $table->char('ticket_hash', 32);                // md5('03,12,25')

            // Optional: associate with a user
            $table->foreignId('user_id')->nullable()
                ->constrained()
                ->onDelete('set null');

            $table->timestamp('issued_at')->nullable();

            $table->timestamps();

            // Ensure no duplicates per campaign
            $table->unique(['campaign_id', 'ticket_hash']);
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_tickets');
    }
}
