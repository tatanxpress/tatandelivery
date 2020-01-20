<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->string('phone', 20)->unique();
            $table->string('email', 100)->unique()->default('');
            $table->string('password', 255);
            $table->string('codigo_correo',10)->default('');
            $table->string('device_id', 100)->default('');
            $table->bigInteger('zonas_id')->unsigned();
            $table->dateTime('fecha');
            $table->boolean('activo')->default(1);

            $table->foreign('zonas_id')->references('id')->on('zonas');
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
