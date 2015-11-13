<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropManufacturingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subcontractors', function (Blueprint $table) {
            //
            $table->dropForeign('subcontractors_manufacturing_id_foreign');
            $table->dropColumn('manufacturing_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subcontractors', function (Blueprint $table) {
            //
            $table->integer('manufacturing_id')->unsigned()->index();
            $table->foreign('manufacturing_id')->references('id')->on('manufacturings')->onDelete('cascade')->onUpdate('cascade');

        });
    }
}
