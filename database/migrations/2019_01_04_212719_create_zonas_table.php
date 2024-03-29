<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonasTable extends Migration
{
    /**
     * Zonas en general
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 50);
            $table->string('descripcion', 200);
            $table->string('latitud', 50);
            $table->string('longitud', 50);
            $table->boolean('saturacion')->default(0);
            $table->time('hora_abierto_delivery');
            $table->time('hora_cerrado_delivery');
            $table->date('fecha');
            $table->boolean('activo');
            $table->string('identificador', 50)->unique();
            $table->integer('tiempo_extra'); // aumenta el tiempo de una orden, a esta zona
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zonas');
    }
}
