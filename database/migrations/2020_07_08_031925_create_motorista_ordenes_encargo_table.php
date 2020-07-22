<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristaOrdenesEncargoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorista_ordenes_encargo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_encargo_id')->unsigned();
            $table->bigInteger('motoristas_id')->unsigned();
            $table->dateTime('fecha_agarrada');
        
            $table->foreign('ordenes_encargo_id')->references('id')->on('ordenes_encargo');
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
        Schema::dropIfExists('motorista_ordenes_encargo');
    }
}
