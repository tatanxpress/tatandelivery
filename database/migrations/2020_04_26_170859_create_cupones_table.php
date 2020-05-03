<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCuponesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cupones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tipo_cupon_id')->unsigned();
            $table->string('texto_cupon', 50)->unique();
            $table->integer('uso_limite');
            $table->integer('contador');
            $table->date('fecha');
            $table->boolean('activo');
            $table->boolean('ilimitado');

            $table->foreign('tipo_cupon_id')->references('id')->on('tipo_cupon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cupones');
    }
}
