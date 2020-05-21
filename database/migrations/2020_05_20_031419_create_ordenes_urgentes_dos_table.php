<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesUrgentesDosTable extends Migration
{
    /**
     * propietarios termina de preparar comida y ningun motorista agarro la orden
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_urgentes_dos', function (Blueprint $table) {
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
        Schema::dropIfExists('ordenes_urgentes_dos');
    }
}
