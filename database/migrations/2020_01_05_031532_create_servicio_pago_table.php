<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicioPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicio_pago', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('servicios_id')->unsigned();
            $table->date('fecha1');
            $table->date('fecha2');
            $table->date('fecha');
            $table->decimal('pago', 10, 2);
            $table->string('descripcion', 200)->default("");

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
        Schema::dropIfExists('servicio_pago');
    }
}
