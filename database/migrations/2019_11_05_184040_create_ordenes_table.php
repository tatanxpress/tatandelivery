<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('users_id')->unsigned();
            $table->bigInteger('servicios_id')->unsigned();
            $table->string('nota_orden', 200)->default('');
            $table->decimal('precio_total', 7,2);
            $table->decimal('precio_envio', 7,2);
            $table->dateTime('fecha_orden');
            $table->string('cambio', 20)->default('');
            $table->boolean('estado_2')->default(0);
            $table->dateTime('fecha_2')->nullable();
            $table->integer('hora_2')->default(0);
            $table->boolean('estado_3')->default(0);
            $table->dateTime('fecha_3')->nullable();
            $table->boolean('estado_4')->default(0);
            $table->dateTime('fecha_4')->nullable();
            $table->boolean('estado_5')->default(0);
            $table->dateTime('fecha_5')->nullable();
            $table->boolean('estado_6')->default(0);
            $table->dateTime('fecha_6')->nullable();
            $table->boolean('estado_7')->default(0);
            $table->dateTime('fecha_7')->nullable();
            $table->boolean('estado_8')->default(0);
            $table->dateTime('fecha_8')->nullable();      
            $table->string('mensaje_8', 200)->default('');
            $table->boolean('visible')->default(0);
            $table->boolean('visible_p')->default(0);
            $table->boolean('visible_p2')->default(0);
            $table->boolean('visible_p3')->default(0); 
            $table->boolean('tardio')->default(0);
            $table->boolean('cancelado_cliente')->default(0);
            $table->boolean('cancelado_propietario')->default(0);
            $table->dateTime('fecha_tardio')->nullable();
            $table->boolean('envio_gratis')->default(0);
            $table->boolean('visible_m')->default(0);
            $table->decimal('ganancia_motorista', 5,2);

            $table->foreign('users_id')->references('id')->on('users');
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
        Schema::dropIfExists('ordenes');
    }
}
