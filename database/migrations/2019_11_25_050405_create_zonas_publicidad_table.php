<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonasPublicidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas_publicidad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('zonas_id')->unsigned();
            $table->bigInteger('publicidad_id')->unsigned();
            $table->integer('posicion');
            $table->date('fecha');
            
            $table->foreign('zonas_id')->references('id')->on('zonas');
            $table->foreign('publicidad_id')->references('id')->on('publicidad');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zonas_publicidad');
    }
}
