<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesProblemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_problemas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_id')->unsigned();
            $table->dateTime('fecha');
            $table->boolean('activo');
            $table->integer('tipo');
 
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
        Schema::dropIfExists('ordenes_problemas');
    }
}
