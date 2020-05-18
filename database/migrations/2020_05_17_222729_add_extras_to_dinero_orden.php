<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtrasToDineroOrden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dinero_orden', function (Blueprint $table) {
            $table->string('correo', 100)->default("info@tatanexpress.com");
            $table->boolean('activo_sms')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dinero_orden', function (Blueprint $table) {
            $table->dropColumn(['correo', 'activo_sms']);
        });
    }
}
