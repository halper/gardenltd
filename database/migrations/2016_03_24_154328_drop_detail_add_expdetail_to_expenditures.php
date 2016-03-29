<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropDetailAddExpdetailToExpenditures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expenditures', function (Blueprint $table) {
            //
            $table->dropColumn('detail');
            $table->unsignedInteger('expdetail_id')->index()->nullable();
            $table->foreign('expdetail_id')->references('id')->on('expdetails')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expenditures', function (Blueprint $table) {
            //
            $table->string('detail');
            $table->dropForeign('expdetail_id');
            $table->dropColumn('expdetail_id');
        });
    }
}
