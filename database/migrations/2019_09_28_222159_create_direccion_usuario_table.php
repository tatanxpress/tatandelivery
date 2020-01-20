<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDireccionUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('direccion_usuario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('numero_casa', 30);
            $table->string('punto_referencia', 400);
            $table->string('telefono', 20);
            $table->boolean('seleccionado');
            $table->string('latitud', 50);
            $table->string('longitud', 50);
            $table->bigInteger('zonas_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
           
            $table->foreign('zonas_id')->references('id')->on('zonas');
            $table->foreign('user_id')->references('id')->on('users');
        });


        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('direccion_usuario');
    }
}
