<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropWorkingPresentBrokenFromEquipmentSite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('equipment_site', function (Blueprint $table) {
            //
            $table->dropColumn('working');
            $table->dropColumn('present');
            $table->dropColumn('broken');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('equipment_site', function (Blueprint $table) {
            //
            $table->integer('working')->unsigned();
            $table->integer('present')->unsigned();
            $table->integer('broken')->unsigned();
        });
    }
}
