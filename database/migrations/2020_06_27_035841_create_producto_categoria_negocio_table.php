<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoCategoriaNegocioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto_categoria_negocio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('categorias_negocio_id')->unsigned();
            $table->string('nombre', 200);
            $table->string('imagen', 100);
            $table->string('descripcion', 500)->nullable();
            $table->decimal('precio', 10,2);
            $table->boolean('utiliza_nota');
            $table->string('nota', 75)->nullable();

            $table->foreign('categorias_negocio_id')->references('id')->on('categorias_negocios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_categoria_negocio');
    }
}
