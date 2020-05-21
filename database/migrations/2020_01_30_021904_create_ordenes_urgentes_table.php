<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesUrgentesTable extends Migration
{
    /**
     * ordenes completadas, aun no sale motorista, ya paso la hora estimada de entrega que dio el propietario + 2 min extra. 
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_urgentes', function (Blueprint $table) {
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
        Schema::dropIfExists('ordenes_urgentes');
    }
}
