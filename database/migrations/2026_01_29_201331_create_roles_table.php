<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment
            $table->string('name', 100)->unique();
            $table->timestamps(); // created_at & updated_at (nullable by default)
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
