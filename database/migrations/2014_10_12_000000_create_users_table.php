<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('zoho_customer_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('street')->nullable();
            $table->string('house_no')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('email_verified')->default(0);
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->string('login_otp')->nullable();
            $table->string('forget_password_token')->nullable();

            // Social login fields
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->text('provider_avatar')->nullable();

            // Profile fields
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->string('address')->nullable();
            $table->string('zip_code')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();

            // Status fields
            $table->integer('status')->default(1);
            $table->integer('is_completed')->default(0);

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
        Schema::dropIfExists('users');
    }
}
