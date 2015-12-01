<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContractDatesToSiteSubcontractor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('site_subcontractor', function (Blueprint $table) {
            //
            $table->date('contract_date');
            $table->date('contract_start_date');
            $table->date('contract_end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('site_subcontractor', function (Blueprint $table) {
            //
            $table->dropColumn('contract_dropColumn');
            $table->dropColumn('contract_start_dropColumn');
            $table->dropColumn('contract_end_dropColumn');
        });
    }
}
