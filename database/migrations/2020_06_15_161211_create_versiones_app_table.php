<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionesAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versiones_app', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('android', 25)->default("1.15");
            $table->string('iphone', 25)->default("1.20");
            $table->boolean('activo')->default(1);
            $table->boolean('activo_iphone')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('versiones_app');
    }
}
