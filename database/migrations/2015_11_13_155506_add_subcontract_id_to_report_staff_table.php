<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubcontractIdToReportStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_staff', function (Blueprint $table) {
            //
            $table->integer('subcontractor_id')->unsigned()->index()->default('1');
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_staff', function (Blueprint $table) {
            //
            $table->dropForeign('report_staff_subcontractor_id');
            $table->dropColumn('subcontractor_id');

        });
    }
}
