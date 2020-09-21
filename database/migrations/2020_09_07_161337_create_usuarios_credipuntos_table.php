<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuariosCredipuntosTable extends Migration
{
    /**
     * transacciones que se haran al ingresar credi puntos
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_credipuntos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('usuario_id')->unsigned();
            $table->decimal('credi_puntos', 10,2);
            $table->decimal('pago_total', 10, 2);
            $table->dateTime('fecha');
            $table->decimal('comision', 10, 2);
            $table->string('nota', 200)->nullable();

            $table->string('idtransaccion', 200)->nullable();
            $table->string('codigo', 200)->nullable();
            
            $table->boolean('esreal');
            $table->boolean('esaprobada');
            $table->boolean('revisada'); // por el admin, el agregara credito manual
            $table->dateTime('fecha_revisada')->nullable();
            $table->foreign('usuario_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usuarios_credipuntos');
    }
}
