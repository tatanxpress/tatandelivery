<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHorariosdireccionToDireccionUsuario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('direccion_usuario', function (Blueprint $table) {
            $table->time('hora_inicio')->default("07:30:00");
            $table->time('hora_fin')->default("17:00:00");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('direccion_usuario', function (Blueprint $table) {
            //
        });
    }
}
