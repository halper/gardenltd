<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSwunitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('swunits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('subcontractor_id')->unsigned()->index();
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('swunits');
    }
}
