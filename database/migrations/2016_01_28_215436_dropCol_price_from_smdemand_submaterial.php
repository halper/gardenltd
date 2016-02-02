<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColPriceFromSmdemandSubmaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smdemand_submaterial', function (Blueprint $table) {
            //
            $table->dropColumn('price');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smdemand_submaterial', function (Blueprint $table) {
            //
            $table->double('price',12,2)->unsigned();
        });
    }
}
