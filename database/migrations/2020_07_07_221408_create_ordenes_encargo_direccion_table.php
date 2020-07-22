<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesEncargoDireccionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_encargo_direccion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_encargo_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();
            $table->string('nombre', 100);
            $table->string('direccion', 400);
            $table->string('numero_casa', 30)->nullable();
            $table->string('punto_referencia', 400)->nullable();
            $table->string('latitud', 50)->nullable();
            $table->string('longitud', 50)->nullable();  
            $table->string('latitud_real', 50)->nullable();
            $table->string('longitud_real', 50)->nullable();
            $table->boolean('revisado')->dafault(0);
            
            $table->foreign('ordenes_encargo_id')->references('id')->on('ordenes_encargo');
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
        Schema::dropIfExists('ordenes_encargo_direccion');
    }
}
