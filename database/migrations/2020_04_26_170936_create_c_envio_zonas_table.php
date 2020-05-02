<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCEnvioZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_envio_zonas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cupones_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();
            $table->foreign('cupones_id')->references('id')->on('cupones');
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
        Schema::dropIfExists('c_envio_zonas');
    }
}
