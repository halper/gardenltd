<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wages', function (Blueprint $table) {
            $table->increments('id');
            $table->double('wage', 6,2);
            $table->date('since')->default('1970-01-01');
            $table->unsignedInteger('personnel_id')->index()->nullable();
            $table->foreign('personnel_id')->references('id')->on('personnel')->onDelete('cascade')->onUptade('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('wages');
    }
}
