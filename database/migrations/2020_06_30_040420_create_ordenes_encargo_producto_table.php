<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesEncargoProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_encargo_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_encargo_id')->unsigned();
            $table->bigInteger('producto_cate_nego_id')->unsigned();
            $table->integer('cantidad');
            $table->string('nota', 400)->nullable();
            $table->decimal('precio', 10,2);
            $table->string('nombre', 200);
            $table->string('descripcion', 500)->nullable();

            $table->foreign('ordenes_encargo_id')->references('id')->on('ordenes_encargo');
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
        Schema::dropIfExists('ordenes_encargo_producto');
    }
}
