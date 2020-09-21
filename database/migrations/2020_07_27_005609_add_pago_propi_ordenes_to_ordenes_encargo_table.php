<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPagoPropiOrdenesToOrdenesEncargoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ordenes_encargo', function (Blueprint $table) {
            $table->boolean("pago_a_propi")->default(0);
            $table->boolean("tipo_pago")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_encargo', function (Blueprint $table) {
            $table->dropColumn(['pago_a_propi']);
        });
    }
}
