<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductoExtrasToProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->string('video_url', 100)->nullable();
            $table->boolean("utiliza_imagen_extra")->default(0);
            $table->boolean("utiliza_video")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn(['video_url']);
            $table->dropColumn(['utiliza_imagen_extra']);
            $table->dropColumn(['utiliza_video']);
        });
    }
}
