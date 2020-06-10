<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOnesignalextraToOrdenesDirecciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_direcciones', function (Blueprint $table) {
            $table->string('movil_ordeno', 10)->default("3");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_direcciones', function (Blueprint $table) {
            $table->dropColumn(['mensaje']);
        });
    }
}
