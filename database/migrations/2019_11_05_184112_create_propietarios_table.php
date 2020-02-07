<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePropietariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propietarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 50);
            $table->string('telefono', 20);
            $table->string('password', 255);
            $table->string('correo', 100)->unique();
            $table->date('fecha');
            $table->boolean('disponibilidad');
            $table->string('device_id',100);
            $table->bigInteger('servicios_id')->unsigned();
            $table->string('codigo_correo', 10);
            $table->boolean('activo');

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
        Schema::dropIfExists('propietarios');
    }
}
