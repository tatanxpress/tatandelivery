<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNuevoCargoToZonaservicio extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zonas_servicios', function (Blueprint $table) {
            $table->decimal('nuevo_cargo', 7,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zonas_servicios', function (Blueprint $table) {
            $table->dropColumn(['nuevo_cargo']);
        });
    }
}
