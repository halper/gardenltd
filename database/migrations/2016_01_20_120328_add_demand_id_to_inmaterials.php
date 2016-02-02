<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDemandIdToInmaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inmaterials', function (Blueprint $table) {
            //
            $table->unsignedInteger('demand_id')->index()->nullable();
            $table->foreign('demand_id')->references('id')->on('demands')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inmaterials', function (Blueprint $table) {
            //
            $table->dropForeign('demand_id');
            $table->dropColumn('demand_id');
        });
    }
}
