<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesDireccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_direcciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('users_id')->unsigned();
            $table->bigInteger('ordenes_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();
            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('numero_casa', 30)->default('');
            $table->string('punto_referencia', 400)->default('');
            $table->string('telefono', 20);
            $table->string('latitud', 50)->default('');
            $table->string('longitud', 50)->default('');
            
            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('ordenes_id')->references('id')->on('ordenes');
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
        Schema::dropIfExists('ordenes_direcciones');
    }
}
