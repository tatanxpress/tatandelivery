<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncargoAsignadoServicioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encargo_asignado_servicio', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('encargos_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();

            $table->foreign('encargos_id')->references('id')->on('encargos');
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
        Schema::dropIfExists('encargo_asignado_servicio');
    }
}
