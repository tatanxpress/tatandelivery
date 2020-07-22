<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristaEncargoAsignadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorista_encargo_asignado', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('encargos_id')->unsigned();
            $table->bigInteger('motoristas_id')->unsigned();
              
            $table->foreign('encargos_id')->references('id')->on('encargos');
            $table->foreign('motoristas_id')->references('id')->on('motoristas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motorista_encargo_asignado');
    }
}
