<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePwunitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pwunits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('staff_id')->unsigned()->index();
            $table->foreign('staff_id')->references('id')->on('staffs')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity');
            $table->text('works_done');
            $table->string('unit');
            $table->integer('planned');
            $table->integer('done');
            $table->integer('report_id')->unsigned()->index();
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('pwunits');
    }
}
