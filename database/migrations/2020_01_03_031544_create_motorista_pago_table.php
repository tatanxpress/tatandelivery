<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMotoristaPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('motorista_pago', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('motorista_id')->unsigned();
            $table->date('fecha1');
            $table->date('fecha2');
            $table->date('fecha');
            $table->decimal('pago', 10, 2);

            $table->foreign('motorista_id')->references('id')->on('motoristas');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('motorista_pago');
    }
}
