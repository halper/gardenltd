<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKdvToSubcontractorsTable extends Migration
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
            $table->unsignedTinyInteger('kdv')->nullable();
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
            $table->dropColumn('kdv');
        });
    }
}
