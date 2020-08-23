<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListaProductoEncargoTable extends Migration
{
    /**
     * aqui se va uniendo los productos a las lista
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lista_producto_encargo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('lista_encargo_id')->unsigned(); // id de la lista
            $table->bigInteger('producto_cate_nego_id')->unsigned(); // id del producto de cualquier negocio
            $table->integer('posicion')->default(1);
            $table->boolean('activo')->default(1);
            
            $table->foreign('lista_encargo_id')->references('id')->on('lista_encargo');
            $table->foreign('producto_cate_nego_id')->references('id')->on('producto_categoria_negocio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lista_producto_encargo');
    }
}
