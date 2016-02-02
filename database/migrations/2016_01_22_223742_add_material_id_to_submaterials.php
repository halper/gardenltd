<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaterialIdToSubmaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submaterials', function (Blueprint $table) {
            //
            $table->unsignedInteger('material_id')->index();
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submaterials', function (Blueprint $table) {
            //
            $table->dropForeign('material_id');
            $table->dropColumn('material_id');
        });
    }
}
