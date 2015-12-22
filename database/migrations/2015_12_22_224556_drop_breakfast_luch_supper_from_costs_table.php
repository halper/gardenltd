<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropBreakfastLuchSupperFromCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('costs', function (Blueprint $table) {
            //
            $table->dropColumn('breakfast');
            $table->dropColumn('lunch');
            $table->dropColumn('supper');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('costs', function (Blueprint $table) {
            $table->double('breakfast', 6, 3)->nullable();
            $table->double('lunch', 6, 3)->nullable();
            $table->double('supper', 6, 3)->nullable();
        });
    }
}
