<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motoristas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 50);
            $table->string('identificador', 50);
            $table->string('telefono', 20)->unique();
            $table->string('correo', 100);
            $table->string('password', 255);
            $table->string('tipo_vehiculo', 50);
            $table->string('numero_vehiculo', 50);
            $table->boolean('activo')->default(1);
            $table->boolean('disponible')->default(0);
            $table->date('fecha');
            $table->string('licensia', 30);
            $table->decimal('limite_dinero', 10, 2);
            $table->string('dui', 25);
            $table->string('imagen', 100);
            $table->string('device_id', 100);
            $table->string('codigo_correo', 10);
            $table->boolean('zona_pago')->default(0); // permite ver las zonas de pago, a los otros motorista
            // no podran ver las zonas de pago, osea donde ir a dejar el dinero
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motoristas');
    }
}
