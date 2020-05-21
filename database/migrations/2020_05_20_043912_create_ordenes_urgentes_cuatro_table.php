<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesUrgentesCuatroTable extends Migration
{
    /**
     *  paso la mitad de tiempo que el propietario dijo que entregarian la orden
     *  ningun motorista agarro la orden
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_urgentes_cuatro', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_id')->unsigned();
            $table->dateTime('fecha');
            $table->boolean('activo');
 
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
        Schema::dropIfExists('ordenes_urgentes_cuatro');
    }
}
