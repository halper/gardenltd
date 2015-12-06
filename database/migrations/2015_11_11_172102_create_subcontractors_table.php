<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubcontractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->increments('id');
            $table->double('price')->unsigned()->nullable();
            $table->integer('subdetail_id')->unsigned()->index();
            $table->foreign('subdetail_id')->references('id')->on('subdetails')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('site_id')->unsigned()->index();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('subcontractors');
    }
}
