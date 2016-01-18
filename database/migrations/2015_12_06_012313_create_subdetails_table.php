<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subdetails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('address');
            $table->integer('city_id')->unsigned()->index();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade')->onUpdate('cascade');
            $table->string('official');
            $table->string('title');
            $table->integer('area_code_id')->unsigned()->index()->null();
            $table->foreign('area_code_id')->references('id')->on('area_codes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('phone')->null();
            $table->integer('fax_code_id')->unsigned()->index()->null();
            $table->foreign('fax_code_id')->references('id')->on('area_codes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('fax')->null();
            $table->integer('mobile_code_id')->unsigned()->index();
            $table->foreign('mobile_code_id')->references('id')->on('mobile_codes')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('mobile');
            $table->string('email');
            $table->string('web')->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number');
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
        Schema::drop('subdetails');
    }
}
