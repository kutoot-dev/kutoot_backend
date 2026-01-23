<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('Sponsor'); // Sponsor, Co-Sponsor, Special Sponsor, Partner
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('link')->nullable();
            $table->integer('serial')->default(0);
            $table->integer('status')->default(1); // 0=inactive, 1=active
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
        Schema::dropIfExists('sponsors');
    }
}
