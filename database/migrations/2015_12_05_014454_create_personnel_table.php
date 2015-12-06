<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonnelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personnel', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('tck_no');
            $table->unique('tck_no');
            $table->string('name');
            $table->integer('personalize_id')->unsigned();
            $table->string('personalize_type');
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
        Schema::drop('personnel');
    }
}
