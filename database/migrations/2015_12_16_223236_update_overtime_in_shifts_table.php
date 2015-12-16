<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOvertimeInShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shifts', function (Blueprint $table) {
            //
            $table->integer('overtime_id')->index()->unsigned()->nullable();
            $table->foreign('overtime_id')->references('id')->on('overtimes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shifts', function (Blueprint $table) {
            //
            $table->dropForeign('overtime_id');
            $table->dropColumn('overtime_id');
        });
    }
}
