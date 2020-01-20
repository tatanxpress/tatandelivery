<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHorarioServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horario_servicio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('servicios_id')->unsigned();
            $table->time('hora1');
            $table->time('hora2');
            $table->time('hora3');
            $table->time('hora4');
            $table->integer('dia');
            $table->boolean('segunda_hora')->default(0);
            $table->boolean('cerrado')->default(0);
            
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
        Schema::dropIfExists('horario_servicio');
    }
}
