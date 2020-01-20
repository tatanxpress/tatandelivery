<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRevisadorMotoristasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revisador_motoristas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('revisador_id')->unsigned();
            $table->bigInteger('motoristas_id')->unsigned();

            $table->foreign('revisador_id')->references('id')->on('revisador');
            $table->foreign('motoristas_id')->references('id')->on('motoristas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('revisador_motoristas');
    }
}
