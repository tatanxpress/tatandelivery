<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicidadProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicidad_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('publicidad_id')->unsigned();
            $table->bigInteger('producto_id')->unsigned();
            $table->foreign('publicidad_id')->references('id')->on('publicidad');
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
        Schema::dropIfExists('publicidad_producto');
    }
}
