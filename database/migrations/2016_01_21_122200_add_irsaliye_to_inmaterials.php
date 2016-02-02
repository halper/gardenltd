<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIrsaliyeToInmaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inmaterials', function (Blueprint $table) {
            //
            $table->string('irsaliye')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inmaterials', function (Blueprint $table) {
            //
            $table->dropColumn('irsaliye');
        });
    }
}
