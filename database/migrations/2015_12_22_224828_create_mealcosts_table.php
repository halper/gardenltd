<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMealcostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mealcosts', function (Blueprint $table) {
            $table->increments('id');
            $table->double('breakfast', 12, 3)->nullable();
            $table->double('lunch', 12, 3)->nullable();
            $table->double('supper', 12, 3)->nullable();
            $table->date('since')->nullable();
            $table->unsignedInteger('site_id')->index()->nullable();
            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::drop('mealcosts');
    }
}
