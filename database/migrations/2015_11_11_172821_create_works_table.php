<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('works', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->double('planned');
            $table->double('done');
            $table->string('unit');
            $table->integer('report_id')->unsigned()->index();
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('workable_id')->unsigned();
            $table->string('workable_type');
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
        Schema::drop('works');
    }
}
