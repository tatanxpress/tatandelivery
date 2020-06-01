<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMensajezonaToZonas extends Migration
{
    /**
     * Mensaje para zona saturacion
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zonas', function (Blueprint $table) {
            $table->string('mensaje', 100)->default("-");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zonas', function (Blueprint $table) {
            $table->dropColumn(['mensaje']);
        });
    }
}
