<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipoServiciosZonas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_servicios_zonas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tipo_servicios_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();
            $table->boolean('activo')->default(1);
            $table->integer('posicion');

            $table->foreign('tipo_servicios_id')->references('id')->on('tipo_servicios');
            $table->foreign('zonas_id')->references('id')->on('zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_servicios_zonas');
    }
}
