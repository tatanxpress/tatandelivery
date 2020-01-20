<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristaOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorista_ordenes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_id')->unsigned();
            $table->bigInteger('motoristas_id')->unsigned();
            $table->dateTime('fecha_agarrada');
            $table->boolean('motorista_prestado');
        
            $table->foreign('ordenes_id')->references('id')->on('ordenes');
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
        Schema::dropIfExists('motorista_ordenes');
    }
}
