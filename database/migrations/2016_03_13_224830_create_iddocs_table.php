<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIddocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iddocs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('personnel_id')->index()->nullable();
            $table->foreign('personnel_id')->references('id')->on('personnel')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('iddocs');
    }
}
