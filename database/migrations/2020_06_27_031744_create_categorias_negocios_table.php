<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriasNegociosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias_negocios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('negocios_encargo_id')->unsigned(); 
            $table->string('nombre', 100);

            $table->foreign('negocios_encargo_id')->references('id')->on('negocios_encargo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias_negocios');
    }
}
