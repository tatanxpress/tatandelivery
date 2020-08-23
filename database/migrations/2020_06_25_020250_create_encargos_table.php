<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncargosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encargos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('identificador', 100)->unique();
            $table->string('nombre', 200);
            $table->string('descripcion', 500)->nullable();
            $table->date('ingreso');
            $table->date('fecha_inicia');
            $table->dateTime('fecha_finaliza');
            $table->dateTime('fecha_entrega'); // para que vea motorista y propietario si es necesario
            $table->boolean('activo'); // activo se muestra, inactivo se ocultara, en todas las zonas asignadas
            $table->string('imagen', 100)();
            $table->integer('tipo_vista'); //0: vertical  1: horizontal
            $table->boolean('permiso_motorista'); // el moto asignado al encargo, ya podra verlo para agarrarlo
            $table->boolean('vista_cliente'); // ocultarlo vista cliente, antes de darle finalizar al encargo, porque
                                                // todas las opciones estan en ventana activos
            $table->boolean('visible_propietario'); // defecto es 0, al tener 1 el propietario pueda ver la tarjeta
                                                    // encargos asi al meterse vera las ordenes_encargo, al terminar las ordenes_encargo
                                                    // podra ocultar la tarjeta encargo.

            $table->string('texto_boton', 90)->default('Guardar'); // texto para el boton del encargo

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encargos');
    }
}
