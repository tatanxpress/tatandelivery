<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesEncargoRevisadasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_encargo_revisadas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_encargo_id')->unsigned(); 
            $table->dateTime('fecha');
            $table->bigInteger('revisador_id')->unsigned();

            $table->foreign('ordenes_encargo_id')->references('id')->on('ordenes_encargo');
            $table->foreign('revisador_id')->references('id')->on('revisador');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_encargo_revisadas');
    }
}
