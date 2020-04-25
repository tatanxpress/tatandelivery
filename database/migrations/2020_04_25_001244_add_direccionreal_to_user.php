<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDireccionrealToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('direccion_usuario', function (Blueprint $table) {
            $table->string('latitud_real', 50)->default("");
            $table->string('longitud_real', 50)->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('direccion_usuario', function (Blueprint $table) {
            $table->dropColumn(['latitud_real',  'longitud_real']);
        });
    }
}
