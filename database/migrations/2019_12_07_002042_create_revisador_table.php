<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisadorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisador', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);            
            $table->string('telefono', 20)->unique();           
            $table->string('password', 255);
            $table->boolean('disponible');
            $table->boolean('activo');
            $table->string('codigo', 10);
            $table->date('fecha');
            $table->string('identificador', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('revisador');
    }
}
