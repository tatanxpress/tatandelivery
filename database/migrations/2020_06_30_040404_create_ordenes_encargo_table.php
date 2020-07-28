<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesEncargoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_encargo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('users_id')->unsigned();
            $table->integer('revisado');
            $table->bigInteger('encargos_id')->unsigned();
            $table->decimal('precio_subtotal', 10,2); //precio del servicio nomas 
            $table->decimal('precio_envio', 7,2); // 
            $table->dateTime('fecha_orden');
            $table->decimal('ganancia_motorista', 5,2);
            $table->boolean('visible_cliente')->default(1);
            $table->boolean('visible_motorista')->default(1);
            $table->boolean('visible_propietario')->default(1);
            $table->string('mensaje_cancelado', 200)->nullable();

            $table->boolean('cancelado_por')->default(0);
            $table->dateTime('fecha_cancelado');

            $table->integer('calificacion')->default(0);
            $table->string('mensaje', 400)->nullable();

            // debe ser seteado por propietario, indicando que inicio la preparacion
            // del encargo
            $table->boolean('estado_0')->default(0);
            $table->dateTime('fecha_0');

            // debe ser seteado para darle permiso al motorista de iniciar la entrega
            // osea aqui ya se termino la orden
            $table->boolean('estado_1')->default(0);
            $table->dateTime('fecha_1');
 
            // cuando motorista va en camino
            $table->boolean('estado_2')->default(0);
            $table->dateTime('fecha_2');

            // motorista completo la orden
            $table->boolean('estado_3')->default(0);
            $table->dateTime('fecha_3');

            $table->foreign('users_id')->references('id')->on('users');
            $table->foreign('encargos_id')->references('id')->on('encargos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_encargo');
    }
}
