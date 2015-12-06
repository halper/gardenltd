<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWeatherTempWindHumidityIsWorkingToReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            //
            $table->string('weather')->nullable();
            $table->double('temp_min')->nullable();
            $table->double('temp_max')->nullable();
            $table->double('wind')->nullable();
            $table->tinyInteger('is_working')->unsigned()->default("1");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            //
            $table->dropColumn('weather');
            $table->dropColumn('temp_min');
            $table->dropColumn('temp_max');
            $table->dropColumn('humidity');
            $table->dropColumn('wind');
            $table->dropColumn('is_working');
        });
    }
}
