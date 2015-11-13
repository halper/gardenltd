<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManufacturingSubcontractor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manufacturing_subcontractor', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('manufacturing_id')->unsigned()->index();
            $table->foreign('manufacturing_id')->references('id')->on('manufacturings')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('subcontractor_id')->unsigned()->index();
            $table->foreign('subcontractor_id')->references('id')->on('subcontractors')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('manufacturing_subcontractor');
    }
}
