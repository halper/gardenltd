<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsSmToSubmaterials extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('submaterials', function (Blueprint $table) {
            //
            $table->unsignedTinyInteger('is_sm')->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('submaterials', function (Blueprint $table) {
            //
            $table->dropColumn('is_sm');
        });
    }
}
