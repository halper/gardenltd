<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemandMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demand_material', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('demand_id')->unsigned()->index();
            $table->foreign('demand_id')->references('id')->on('demands')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('material_id')->unsigned()->index();
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('quantity')->unsigned();
            $table->string('unit');
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
        Schema::drop('demand_material');
    }
}
