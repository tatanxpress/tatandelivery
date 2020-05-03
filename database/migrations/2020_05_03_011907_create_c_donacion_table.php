<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCDonacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_donacion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('cupones_id')->unsigned();
            $table->bigInteger('instituciones_id')->unsigned();
            $table->decimal('dinero', 7,2);

            $table->foreign('cupones_id')->references('id')->on('cupones');
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
        Schema::dropIfExists('c_donacion');
    }
}
