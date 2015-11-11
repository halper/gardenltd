<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddManagementEmployerBuildingControlStaff extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            //
            $table->integer('management_staff')->unsigned()->nullable();
            $table->integer('employer_staff')->unsigned()->nullable();
            $table->integer('building_control_staff')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            //
            $table->dropColumn('management_staff');
            $table->dropColumn('employer_staff');
            $table->dropColumn('building_control_staff');
        });
    }
}
