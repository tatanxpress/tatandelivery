<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAplicaCuponDonacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aplica_cupon_donacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ordenes_id')->unsigned();
            $table->bigInteger('instituciones_id')->unsigned();
            $table->decimal('dinero', 7,2);

            $table->foreign('ordenes_id')->references('id')->on('ordenes');
            $table->foreign('instituciones_id')->references('id')->on('instituciones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aplica_cupon_donacion');
    }
}
