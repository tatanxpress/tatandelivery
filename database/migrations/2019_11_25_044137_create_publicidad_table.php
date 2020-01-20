<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePublicidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publicidad', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('descripcion', 100);
            $table->string('logo', 100);
            $table->string('imagen', 100);
            $table->integer('tipo_publicidad');
            $table->string('url_facebook', 300);
            $table->boolean('utiliza_facebook');
            $table->string('url_youtube', 300);
            $table->boolean('utiliza_youtube');
            $table->string('url_instagram', 300);
            $table->boolean('utiliza_instagram');
            $table->string('titulo', 300);
            $table->boolean('utiliza_titulo');
            $table->string('titulo_descripcion', 800);
            $table->boolean('utiliza_descripcion');
            $table->string('telefono', 20);
            $table->boolean('utiliza_telefono');
            $table->boolean('activo');
            $table->boolean('utiliza_visitanos');
            $table->string('visitanos', 50);
            $table->string('identificador', 50)->unique();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publicidad');
    }
}
