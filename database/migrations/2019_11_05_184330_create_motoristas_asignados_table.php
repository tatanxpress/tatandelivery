<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristasAsignadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motoristas_asignados', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('servicios_id')->unsigned();
            $table->bigInteger('motoristas_id')->unsigned();
              
            $table->foreign('servicios_id')->references('id')->on('servicios');
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
        Schema::dropIfExists('motoristas_asignados');
    }
}
