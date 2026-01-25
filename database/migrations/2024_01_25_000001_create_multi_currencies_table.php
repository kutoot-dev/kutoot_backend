<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multi_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('currency_name');
            $table->string('country_code', 10)->nullable();
            $table->string('currency_code', 10);
            $table->string('currency_icon', 20)->nullable();
            $table->decimal('currency_rate', 10, 4)->default(1.0000);
            $table->enum('is_default', ['Yes', 'No'])->default('No');
            $table->enum('currency_position', ['left', 'right'])->default('left');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('multi_currencies');
    }
};
