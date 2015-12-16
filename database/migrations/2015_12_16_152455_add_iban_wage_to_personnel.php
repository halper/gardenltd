<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIbanWageToPersonnel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('personnel', function (Blueprint $table) {
            //
            $table->string("iban")->nullable();;
            $table->double("wage", 4, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('personnel', function (Blueprint $table) {
            //
            $table->dropColumn("iban");
            $table->dropColumn("wage");
        });
    }
}
