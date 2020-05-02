<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCProductoGratisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_producto_gratis', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->bigInteger('cupones_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();
            $table->decimal('dinero_carrito', 7, 2);
            $table->string('nombre', 50);
            $table->foreign('cupones_id')->references('id')->on('cupones');
            $table->foreign('servicios_id')->references('id')->on('servicios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_producto_gratis');
    }
}
