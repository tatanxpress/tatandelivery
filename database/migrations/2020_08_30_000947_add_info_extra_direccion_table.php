<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInfoExtraDireccionTable extends Migration
{
    /**
     * informacion nueva para tabla direccion_usuario
     * cuando se registra una persona fuera del pais
     * @return void
     */
    public function up()
    {
        Schema::table('direccion_usuario', function (Blueprint $table) {
          
            // 0: no tomar en cuenta
            // 1: verificada
            // 2: rechazada
            $table->integer('estado')->default(0); 
            
            // precio a tomar de esta direccion
            $table->decimal('precio_envio', 10,2)->nullable()->default(0);
             // ganancia motorista
             $table->decimal('ganancia_motorista', 10,2)->nullable()->default(0);

            // mensaje porque se rechazo la direccion
            $table->string('mensaje_rechazo', 200)->nullable()->default("");
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // no agregar nada aqui
    }
}
