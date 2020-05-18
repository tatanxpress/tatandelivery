<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNumerosSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('numeros_sms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('area', 50);
            $table->string('numero', 50);
            $table->string('codigo', 10);
            $table->string('codigo_fijo', 10);
            $table->integer('contador');
            $table->date('fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */ 
    public function down()
    {
        Schema::dropIfExists('numeros_sms');
    }
}
