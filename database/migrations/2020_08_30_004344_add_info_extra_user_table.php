<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInfoExtraUserTable extends Migration
{
    /**
     * monedero para los credi puntos
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('monedero', 10,2)->default(0);
            $table->string('area', 20)->nullable()->default("+503");
        });
    }
 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //            

    }
}
