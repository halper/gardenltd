<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalBidCostChangePrice extends Migration
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
            $table->double('additional_bid_cost', 14,2)->nullable();
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
            $table->dropColumn('additional_bid_cost');
        });
    }
}
