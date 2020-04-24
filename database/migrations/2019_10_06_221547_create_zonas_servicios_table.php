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
            $table->decimal('precio_envio', 10,2);
            $table->boolean('activo')->default(1);
            $table->decimal('ganancia_motorista', 10,2);
            $table->date('fecha');
            $table->integer('posicion');

            // servicios privados dicen su horario de entrega por zona
            $table->boolean('tiempo_limite')->default(0);
            $table->time('horario_inicio')->default("07:00:00");
            $table->time('horario_final')->default("21:00:00");

            // si supera el minimo de compra, su envio es gratis
            $table->boolean('min_envio_gratis')->default(0);
            $table->decimal('costo_envio_gratis', 10,2);

            // si el servicio dara envio gratis a esta zona, sin tocar el precio envio
            $table->boolean('zona_envio_gratis')->default(0);

            // mitad de precio de envio
            $table->boolean('mitad_precio')->default(0);

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
