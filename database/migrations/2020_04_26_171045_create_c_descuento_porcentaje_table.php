<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCDescuentoPorcentajeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_descuento_porcentaje', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->bigInteger('cupones_id')->unsigned();
            $table->integer('porcentaje');
            $table->decimal('dinero', 7,2);
            $table->foreign('cupones_id')->references('id')->on('cupones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('c_descuento_porcentaje');
    }
}
