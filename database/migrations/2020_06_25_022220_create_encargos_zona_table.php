<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEncargosZonaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('encargos_zona', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('encargos_id')->unsigned(); // por el encargo
            $table->bigInteger('zonas_id')->unsigned(); // a una zona
            $table->decimal('precio_envio', 10,2);
            $table->decimal('ganancia_motorista', 10,2);

            $table->foreign('encargos_id')->references('id')->on('encargos');
            $table->foreign('zonas_id')->references('id')->on('zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('encargos_zona');
    }
}
