<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarritoEncargoProTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_encargo_pro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('carrito_encargo_id')->unsigned(); 
            $table->bigInteger('producto_cate_nego_id')->unsigned(); 
            $table->string('nota_producto', 400)->nullable();
            $table->integer('cantidad');               

            $table->foreign('carrito_encargo_id')->references('id')->on('carrito_encargo');
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
        Schema::dropIfExists('carrito_encargo_pro');
    }
}
