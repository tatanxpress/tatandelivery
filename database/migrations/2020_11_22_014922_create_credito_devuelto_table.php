<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditoDevueltoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credito_devuelto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_id')->unsigned();

            $table->foreign('ordenes_id')->references('id')->on('ordenes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credito_devuelto');
    }
}
