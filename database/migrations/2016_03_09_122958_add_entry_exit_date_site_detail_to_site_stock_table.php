<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntryExitDateSiteDetailToSiteStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_stock', function (Blueprint $table) {
            //
            $table->date('entry_date')->default('2016-01-01');
            $table->date('exit_date')->default('2016-01-01');
            $table->string('site_detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_stock', function (Blueprint $table) {
            //
            $table->dropColumn('entry_date');
            $table->dropColumn('exit_date');
            $table->dropColumn('site_detail');
        });
    }
}
