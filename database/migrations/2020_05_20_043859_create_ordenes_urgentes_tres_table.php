<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesUrgentesTresTable extends Migration
{
    /**
     * pasaron 5+ de hora entrega al cliente (hora_2 + zona + 5+) y no se ha entregado su orden
     * tabla ordenes_urgentes_tres
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_urgentes_tres', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_id')->unsigned();
            $table->dateTime('fecha');
            $table->boolean('activo');
            $table->integer('tipo');
 
            $table->foreign('ordenes_id')->references('id')->on('ordenes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_urgentes_tres');
    }
}
