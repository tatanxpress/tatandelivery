<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarritoEncargoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carrito_encargo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('users_id')->unsigned();
            $table->bigInteger('encargos_id')->unsigned();
            $table->bigInteger('zonas_id')->unsigned();
            
            $table->foreign('users_id')->references('id')->on('users');
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
        Schema::dropIfExists('carrito_encargo');
    }
}
