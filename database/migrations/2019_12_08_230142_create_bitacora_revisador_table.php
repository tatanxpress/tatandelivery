<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBitacoraRevisadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bitacora_revisador', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('revisador_id')->unsigned();
            $table->date('fecha1');
            $table->date('fecha2');
            $table->decimal('total', 10,2);
            $table->integer('confirmadas');

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
        Schema::dropIfExists('bitacora_revisador');
    }
}
