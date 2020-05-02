<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCDescuentoDineroServicios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_descuento_dinero_servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cupones_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();
            $table->foreign('cupones_id')->references('id')->on('cupones');
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
        Schema::dropIfExists('c_descuento_dinero_servicios');
    }
}
