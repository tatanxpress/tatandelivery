<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoligonoArrayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poligono_array', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('latitud', 50);
            $table->string('longitud', 50);
            $table->bigInteger('zonas_id')->unsigned();
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
        Schema::dropIfExists('poligono_array');
    }
}
