<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonasServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonas_servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('zonas_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();
            $table->decimal('precio_envio', 5,2);
            $table->boolean('activo')->default(1);
            $table->decimal('ganancia_motorista', 5,2);
            $table->date('fecha');
            $table->integer('posicion');

            $table->foreign('zonas_id')->references('id')->on('zonas');
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
        Schema::dropIfExists('zonas_servicios');
    }
}
