<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeatureSubmaterialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_submaterial', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('feature_id')->index()->nullable();
            $table->foreign('feature_id')->references('id')->on('features')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('submaterial_id')->index()->nullable();
            $table->foreign('submaterial_id')->references('id')->on('submaterials')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::drop('feature_submaterial');
    }
}
