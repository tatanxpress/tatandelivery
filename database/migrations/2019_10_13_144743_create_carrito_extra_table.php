<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarritoExtraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_extra', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('carrito_temporal_id')->unsigned();
            $table->bigInteger('producto_id')->unsigned();
            $table->string('nota_producto', 200)->default('');
            $table->integer('cantidad')->default('0');

            $table->foreign('carrito_temporal_id')->references('id')->on('carrito_temporal');
            $table->foreign('producto_id')->references('id')->on('producto');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carrito_extra');
    }
}
