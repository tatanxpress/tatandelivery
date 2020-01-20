<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagoPropietarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pago_propietario', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha');
            $table->string('total_ordenes', 100);
            $table->string('fecha_pago', 100);
            $table->string('completadas', 100);
            $table->string('cancelada_propietario', 100);
            $table->string('cancelada_cliente', 100);
            $table->string('cancelada_tardio', 100);
            $table->string('total_generado', 100);
            $table->string('descuento', 100);
            $table->string('total', 100);
            $table->string('nota', 1000);
            $table->boolean('visible');

            $table->bigInteger('servicios_id')->unsigned();
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
        Schema::dropIfExists('pago_propietario');
    }
}
