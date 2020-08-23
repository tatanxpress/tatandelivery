<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListaEncargoTable extends Migration
{
    /**
     * aqui va la categoria vinculada a la tarjeta encargo
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lista_encargo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('categorias_negocios_id')->unsigned(); // id de categoria
            $table->bigInteger('encargos_id')->unsigned(); // id de categoria
            $table->integer('posicion')->default(1);
            $table->boolean('activo')->default(1);

            $table->foreign('categorias_negocios_id')->references('id')->on('categorias_negocios');
            $table->foreign('encargos_id')->references('id')->on('encargos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lista_encargo');
    }
}
