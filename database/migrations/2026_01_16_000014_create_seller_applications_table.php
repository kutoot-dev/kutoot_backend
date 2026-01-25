<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellerApplicationsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('seller_applications')) {
            return;
        }

        Schema::create('seller_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_id')->unique(); // Format: KT-XXXXXX
            $table->string('store_name');
            $table->string('owner_mobile', 15);
            $table->string('owner_email')->nullable();
            $table->string('store_type')->nullable(); // Store category
            $table->string('store_address');
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('min_bill_amount', 10, 2)->default(0);
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('no_of_ratings')->default(0);
            $table->string('store_image')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['PENDING', 'VERIFIED', 'APPROVED', 'REJECTED'])->default('PENDING');

            // Verification details
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamp('verified_at')->nullable();

            // Approval details
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('seller_email')->nullable();
            $table->timestamp('approved_at')->nullable();

            // Rejection details
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // Reference to created seller (after approval)
            $table->unsignedBigInteger('seller_id')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('owner_mobile');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seller_applications');
    }
}

