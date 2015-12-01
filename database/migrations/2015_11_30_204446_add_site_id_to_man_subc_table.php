<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiteIdToManSubcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manufacturing_subcontractor', function (Blueprint $table) {
            //
            $table->integer('site_id')->unsigned()->index()->nullable();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manufacturing_subcontractor', function (Blueprint $table) {
            //
            $table->dropForeign('manufacturing_subcontractor_site_id_foreign');
            $table->dropColumn('site_id');
        });
    }
}
