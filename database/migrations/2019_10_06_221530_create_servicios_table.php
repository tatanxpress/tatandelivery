<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 50);
            $table->string('identificador', 50)->unique();
            $table->string('descripcion', 300);  
            $table->string('descripcion_corta', 100);      
            $table->string('logo', 100);
            $table->string('imagen', 100);
            $table->boolean('cerrado_emergencia');
            $table->date('fecha');
            $table->boolean('activo')->default(1);
            $table->bigInteger('tipo_servicios_id')->unsigned();
            $table->boolean('envio_gratis');
            $table->string('telefono', 20);
            $table->string('latitud', 50);
            $table->string('longitud', 50);
            $table->string('direccion', 300);
            $table->boolean('tipo_vista');
            $table->decimal('minimo', 5,2);
            $table->boolean('utiliza_minimo');
            $table->boolean('orden_automatica');
            $table->boolean('producto_visible'); 
            $table->decimal('comision', 5,2);
            $table->integer('tiempo'); // tiempo espera para contestacion automatica
            $table->boolean('privado'); 
            
            $table->foreign('tipo_servicios_id')->references('id')->on('tipo_servicios')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicios');
    }
}
