<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('servicios_tipo_id')->unsigned();
            $table->string('nombre', 50);
            $table->string('imagen', 100);
            $table->string('descripcion', 500)->default("");
            $table->decimal('precio', 10,2);
            $table->integer('unidades');
            $table->boolean('disponibilidad');
            $table->boolean('activo');
            $table->integer('posicion');
            $table->boolean('utiliza_cantidad');
            $table->date('fecha');
            $table->boolean('es_promocion');
            $table->boolean('limite_orden');
            $table->integer('cantidad_por_orden');
            $table->boolean('utiliza_nota');
            $table->string('nota', 50)->default("");
            $table->boolean('utiliza_imagen');
            
            $table->foreign('servicios_tipo_id')->references('id')->on('servicios_tipo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto');
    }
}
